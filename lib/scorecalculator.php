<?php
class ScoreCalculator
{
    /**
     * Determines if a category name designates the summary "Total Ranking" category.
     * 
     * @param string $categoryName
     * @return bool
     */
    public static function isTotalRanking(string $categoryName): bool
    {
        return strcasecmp(trim($categoryName), 'total ranking') === 0;
    }

    /**
     * Determines division (e.g. 'FEMALE', 'MALE', or '') from cand_category or cand_no suffix.
     * 
     * @param string $candNo
     * @param string|null $candCategory
     * @return string
     */
    public static function determineDivision(string $candNo, ?string $candCategory = null): string
    {
        if (!empty($candCategory) && strtoupper(trim($candCategory)) !== 'NONE') {
            return strtoupper(trim($candCategory));
        }

        $trimmed = strtoupper(trim($candNo));
        $lastChar = substr($trimmed, -1);
        if ($lastChar === 'F') {
            return 'FEMALE';
        }
        if ($lastChar === 'M') {
            return 'MALE';
        }

        return '';
    }

    /**
     * Normalizes a candidate category/division key (e.g. 'FEMALE', 'MALE', or '').
     * 
     * @param string|null $candCategory
     * @param string $candNo
     * @return string
     */
    public static function normalizeGroupKey(?string $candCategory, string $candNo = ''): string
    {
        if (!empty($candCategory) && strtoupper(trim($candCategory)) !== 'NONE') {
            return strtoupper(trim($candCategory));
        }

        if (!empty($candNo)) {
            return self::determineDivision($candNo, $candCategory);
        }

        return '';
    }

    /**
     * Checks if the optional 'cand_category' column exists in tbl_candidates.
     * 
     * @param mysqli $conn
     * @return bool
     */
    public static function hasCategoryColumn(mysqli $conn): bool
    {
        static $hasCol = null;
        if ($hasCol === null) {
            $checkCol = $conn->query("SHOW COLUMNS FROM tbl_candidates LIKE 'cand_category'");
            $hasCol = ($checkCol && $checkCol->num_rows > 0);
        }
        return $hasCol;
    }

    /**
     * Checks if any allowed candidates have a category assigned.
     * 
     * @param mysqli $conn
     * @return bool
     */
    public static function hasAnyCategoryAssigned(mysqli $conn): bool
    {
        if (!self::hasCategoryColumn($conn)) {
            return false;
        }

        $sql = "SELECT COUNT(*) as cnt FROM tbl_candidates 
                WHERE status = 'Allow' 
                  AND cand_category IS NOT NULL 
                  AND TRIM(cand_category) != '' 
                  AND UPPER(TRIM(cand_category)) != 'NONE'";
        $res = $conn->query($sql);
        return ($res && ($row = $res->fetch_assoc()) && $row['cnt'] > 0);
    }

    /**
     * Formats a floating-point score to standard 2 decimal places.
     * 
     * @param float $score
     * @return string
     */
    public static function format(float $score): string
    {
        return number_format($score, 2, '.', '');
    }

    /**
     * Computes the arithmetic average score given by all judges for a specific criteria.
     * 
     * Formula: Criteria Average = AVG(score_points)
     * 
     * @param mysqli $conn
     * @param int|string $candId
     * @param int|string $criteriaId
     * @return float
     */
    public static function getCriteriaAverage(mysqli $conn, $candId, $criteriaId): float
    {
        $candId = (int)$candId;
        $criteriaId = (int)$criteriaId;

        $sql = "SELECT CAST(AVG(s.score_points) AS DECIMAL(10,2)) AS score_points 
                FROM tbl_scores AS s 
                WHERE s.cand_id = '$candId' AND s.criteria_id = '$criteriaId'";
                
        $result = $conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return is_null($row['score_points']) ? 0.0 : (float)$row['score_points'];
        }

        return 0.0;
    }

    /**
     * Computes the raw category total (sum of criteria averages) for a candidate.
     * 
     * Formula: Raw Score = SUM(Criteria Averages)
     * 
     * @param mysqli $conn
     * @param int|string $candId
     * @param int|string $categoryId
     * @return float
     */
    public static function getCategoryRawScore(mysqli $conn, $candId, $categoryId): float
    {
        $candId = (int)$candId;
        $categoryId = (int)$categoryId;

        $sqlCriteria = "SELECT criteria_id FROM tbl_criteria 
                        WHERE category_id = '$categoryId' AND status = 'Show' 
                        ORDER BY criteria_id ASC";
        $resCrit = $conn->query($sqlCriteria);

        $rawTotal = 0.0;
        if ($resCrit && $resCrit->num_rows > 0) {
            while ($rowCrit = $resCrit->fetch_assoc()) {
                $rawTotal += self::getCriteriaAverage($conn, $candId, $rowCrit['criteria_id']);
            }
        }

        return round($rawTotal, 2);
    }

    /**
     * Computes the factored (weighted) category score for a candidate.
     * 
     * Formula: Factored Score = (Percentage / 100) * Raw Score
     * 
     * @param mysqli $conn
     * @param int|string $candId
     * @param int|string $categoryId
     * @param float|null $percentage If null, fetched automatically from tbl_category
     * @return float
     */
    public static function getCategoryFactoredScore(mysqli $conn, $candId, $categoryId, ?float $percentage = null): float
    {
        $candId = (int)$candId;
        $categoryId = (int)$categoryId;

        if ($percentage === null) {
            $sqlCat = "SELECT percentage, category_name FROM tbl_category WHERE category_id = '$categoryId'";
            $resCat = $conn->query($sqlCat);
            if ($resCat && $rowCat = $resCat->fetch_assoc()) {
                if (self::isTotalRanking($rowCat['category_name'])) {
                    // For Total Ranking, it equals the grand total of other regular categories
                    return self::getCandidateGrandTotal($conn, $candId);
                }
                $percentage = (float)$rowCat['percentage'];
            } else {
                $percentage = 100.0;
            }
        }

        $rawScore = self::getCategoryRawScore($conn, $candId, $categoryId);
        return round(($percentage / 100.0) * $rawScore, 2);
    }

    /**
     * Fetches all visible competition categories.
     * 
     * @param mysqli $conn
     * @param bool $includeTotalRanking If false, excludes 'Total Ranking'
     * @return array
     */
    public static function getActiveCategories(mysqli $conn, bool $includeTotalRanking = true): array
    {
        $sql = "SELECT category_id, category_name, percentage, status 
                FROM tbl_category 
                WHERE status = 'Show' 
                ORDER BY category_id ASC";
        $res = $conn->query($sql);
        $categories = [];

        if ($res && $res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $row['percentage'] = (float)$row['percentage'];
                $isTR = self::isTotalRanking($row['category_name']);
                if (!$includeTotalRanking && $isTR) {
                    continue;
                }
                $categories[] = $row;
            }
        }

        return $categories;
    }

    /**
     * Computes the total sum of percentage weights across all regular categories.
     * 
     * @param mysqli $conn
     * @return float
     */
    public static function getTotalPercentageWeight(mysqli $conn): float
    {
        $categories = self::getActiveCategories($conn, false);
        $totalWeight = 0.0;
        foreach ($categories as $cat) {
            $totalWeight += (float)$cat['percentage'];
        }
        return $totalWeight;
    }

    /**
     * Computes the complete breakdown of factored category scores and grand total for a candidate.
     * 
     * @param mysqli $conn
     * @param int|string $candId
     * @return array ['regular_scores' => [catId => score], 'grand_total' => float, 'normalized_score' => float]
     */
    public static function getCandidateScoreBreakdown(mysqli $conn, $candId): array
    {
        $candId = (int)$candId;
        $categories = self::getActiveCategories($conn, false);

        $regularScores = [];
        $grandTotal = 0.0;

        foreach ($categories as $cat) {
            $catId = (int)$cat['category_id'];
            $factored = self::getCategoryFactoredScore($conn, $candId, $catId, (float)$cat['percentage']);
            $regularScores[$catId] = $factored;
            $grandTotal += $factored;
        }

        $totalWeight = self::getTotalPercentageWeight($conn);
        $normalizedScore = ($totalWeight > 0.0) ? round(($grandTotal / $totalWeight) * 100.0, 2) : 0.0;

        return [
            'regular_scores'   => $regularScores,
            'grand_total'      => round($grandTotal, 2),
            'normalized_score' => $normalizedScore,
        ];
    }

    /**
     * Computes the grand total score of a candidate (sum of all regular factored scores).
     * 
     * @param mysqli $conn
     * @param int|string $candId
     * @return float
     */
    public static function getCandidateGrandTotal(mysqli $conn, $candId): float
    {
        $breakdown = self::getCandidateScoreBreakdown($conn, $candId);
        return $breakdown['grand_total'];
    }

    /**
     * Computes overall rankings across all allowed candidates, with optional division filtering.
     * 
     * @param mysqli $conn
     * @param string|null $filterGroup If provided (e.g. 'FEMALE', 'MALE'), filters by normalized division
     * @return array Sorted array of candidate entries in descending order of score
     */
    public static function getOverallRankings(mysqli $conn, ?string $filterGroup = null): array
    {
        $sqlCand = "SELECT * FROM tbl_candidates 
                    WHERE status = 'Allow' 
                    ORDER BY cand_no ASC";
        $resCand = $conn->query($sqlCand);

        $entries = [];

        if ($resCand && $resCand->num_rows > 0) {
            while ($candRow = $resCand->fetch_assoc()) {
                $candGroup = self::normalizeGroupKey($candRow['cand_category'] ?? null, $candRow['cand_no']);

                if ($filterGroup !== null && $candGroup !== $filterGroup) {
                    continue;
                }

                $breakdown = self::getCandidateScoreBreakdown($conn, $candRow['cand_id']);

                $entries[] = [
                    'cand_id'          => (int)$candRow['cand_id'],
                    'cand_no'          => $candRow['cand_no'],
                    'cand_name'        => $candRow['cand_name'],
                    'cand_category'    => $candRow['cand_category'] ?? '',
                    'group_key'        => $candGroup,
                    'grand_total'      => $breakdown['grand_total'],
                    'score'            => $breakdown['normalized_score'],
                    'category_scores'  => $breakdown['regular_scores'],
                ];
            }
        }

        // Sort descending by normalized score, then grand total as secondary tie-breaker
        usort($entries, function ($a, $b) {
            if ($a['score'] == $b['score']) {
                return $b['grand_total'] <=> $a['grand_total'];
            }
            return $b['score'] <=> $a['score'];
        });

        // Assign ranks
        $rank = 1;
        foreach ($entries as $index => &$entry) {
            $entry['rank'] = $rank++;
        }

        return $entries;
    }

    /**
     * Computes candidate rankings for a specific category.
     * 
     * @param mysqli $conn
     * @param int|string $categoryId
     * @param string|null $filterGroup
     * @return array Sorted array of candidate entries in descending order of category score
     */
    public static function getCategoryRankings(mysqli $conn, $categoryId, ?string $filterGroup = null): array
    {
        $categoryId = (int)$categoryId;
        $sqlCand = "SELECT * FROM tbl_candidates 
                    WHERE status = 'Allow' 
                    ORDER BY cand_no ASC";
        $resCand = $conn->query($sqlCand);

        $entries = [];

        if ($resCand && $resCand->num_rows > 0) {
            while ($candRow = $resCand->fetch_assoc()) {
                $candGroup = self::normalizeGroupKey($candRow['cand_category'] ?? null, $candRow['cand_no']);

                if ($filterGroup !== null && $candGroup !== $filterGroup) {
                    continue;
                }

                $factoredScore = self::getCategoryFactoredScore($conn, $candRow['cand_id'], $categoryId);
                $rawScore = self::getCategoryRawScore($conn, $candRow['cand_id'], $categoryId);

                $entries[] = [
                    'cand_id'       => (int)$candRow['cand_id'],
                    'cand_no'       => $candRow['cand_no'],
                    'cand_name'     => $candRow['cand_name'],
                    'cand_category' => $candRow['cand_category'] ?? '',
                    'group_key'     => $candGroup,
                    'raw_score'      => $rawScore,
                    'factored_score' => $factoredScore,
                    'score'          => $rawScore,
                ];
            }
        }

        usort($entries, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        $rank = 1;
        foreach ($entries as $index => &$entry) {
            $entry['rank'] = $rank++;
        }

        return $entries;
    }

    /**
     * Computes the total score submitted by an individual judge for a candidate under a category.
     * Used in Judge Workspace review tabs.
     * 
     * @param mysqli $conn
     * @param int|string $judgeId
     * @param int|string $candId
     * @param int|string $categoryId
     * @return float
     */
    public static function getJudgeCategoryTotal(mysqli $conn, $judgeId, $candId, $categoryId): float
    {
        $judgeId = (int)$judgeId;
        $candId = (int)$candId;
        $categoryId = (int)$categoryId;

        $sql = "SELECT SUM(score_points) as cat_total 
                FROM tbl_scores 
                WHERE user_id = '$judgeId' 
                  AND cand_id = '$candId' 
                  AND category_id = '$categoryId'";
                  
        $result = $conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return is_null($row['cat_total']) ? 0.0 : (float)$row['cat_total'];
        }

        return 0.0;
    }

    /**
     * Retrieves the score submitted by an individual judge for a specific criterion.
     * 
     * @param mysqli $conn
     * @param int|string $judgeId
     * @param int|string $candId
     * @param int|string $criteriaId
     * @return float
     */
    public static function getJudgeCriteriaScore(mysqli $conn, $judgeId, $candId, $criteriaId): float
    {
        $judgeId = (int)$judgeId;
        $candId = (int)$candId;
        $criteriaId = (int)$criteriaId;

        $sql = "SELECT score_points FROM tbl_scores 
                WHERE user_id = '$judgeId' 
                  AND cand_id = '$candId' 
                  AND criteria_id = '$criteriaId' 
                LIMIT 1";

        $result = $conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return is_null($row['score_points']) ? 0.0 : (float)$row['score_points'];
        }

        return 0.0;
    }
}
