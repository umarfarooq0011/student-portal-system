<?php
require_once __DIR__ . '/../config/db.php';

class Analytics
{
    // Get grade distribution (A, B, C, D, F)
    public function getGradeDistribution()
    {
        global $conn;
        $query = "SELECT
            CASE
                WHEN grade IS NULL OR grade = '' THEN 'Ungraded'
                WHEN CAST(SUBSTRING_INDEX(grade, '/', 1) AS DECIMAL) /
                     CAST(SUBSTRING_INDEX(grade, '/', -1) AS DECIMAL) >= 0.9 THEN 'A'
                WHEN CAST(SUBSTRING_INDEX(grade, '/', 1) AS DECIMAL) /
                     CAST(SUBSTRING_INDEX(grade, '/', -1) AS DECIMAL) >= 0.8 THEN 'B'
                WHEN CAST(SUBSTRING_INDEX(grade, '/', 1) AS DECIMAL) /
                     CAST(SUBSTRING_INDEX(grade, '/', -1) AS DECIMAL) >= 0.7 THEN 'C'
                WHEN CAST(SUBSTRING_INDEX(grade, '/', 1) AS DECIMAL) /
                     CAST(SUBSTRING_INDEX(grade, '/', -1) AS DECIMAL) >= 0.6 THEN 'D'
                ELSE 'F'
            END as letter_grade,
            COUNT(*) as count
        FROM submissions
        GROUP BY letter_grade
        ORDER BY FIELD(letter_grade, 'A', 'B', 'C', 'D', 'F', 'Ungraded')";

        $result = $conn->query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    // Get submission trends (last 30 days)
    public function getSubmissionTrends($days = 30)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT DATE(submitted_at) as date, COUNT(*) as count
            FROM submissions
            WHERE submitted_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            GROUP BY DATE(submitted_at)
            ORDER BY date ASC");
        $stmt->bind_param("i", $days);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    // Get top students by average grade
    public function getTopStudents($limit = 10)
    {
        global $conn;
        $query = "SELECT u.id, u.full_name, u.profile_image,
            COUNT(s.id) as total_submissions,
            SUM(CASE WHEN s.grade IS NOT NULL AND s.grade != '' THEN 1 ELSE 0 END) as graded_submissions,
            AVG(
                CASE WHEN s.grade IS NOT NULL AND s.grade != '' THEN
                    CAST(SUBSTRING_INDEX(s.grade, '/', 1) AS DECIMAL) /
                    CAST(SUBSTRING_INDEX(s.grade, '/', -1) AS DECIMAL) * 100
                ELSE NULL END
            ) as avg_grade
        FROM users u
        LEFT JOIN submissions s ON u.id = s.student_id
        WHERE u.role = 'student'
        GROUP BY u.id
        HAVING total_submissions > 0
        ORDER BY avg_grade DESC, total_submissions DESC
        LIMIT ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    // Get subject performance statistics
    public function getSubjectStats()
    {
        global $conn;
        $query = "SELECT a.subject,
            COUNT(DISTINCT a.id) as total_assignments,
            COUNT(s.id) as total_submissions,
            AVG(
                CASE WHEN s.grade IS NOT NULL AND s.grade != '' THEN
                    CAST(SUBSTRING_INDEX(s.grade, '/', 1) AS DECIMAL) /
                    CAST(SUBSTRING_INDEX(s.grade, '/', -1) AS DECIMAL) * 100
                ELSE NULL END
            ) as avg_grade
        FROM assignments a
        LEFT JOIN submissions s ON a.id = s.assignment_id
        GROUP BY a.subject
        ORDER BY total_submissions DESC";

        $result = $conn->query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    // Get overall statistics
    public function getOverallStats()
    {
        global $conn;

        $stats = [];

        // Total students
        $result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'student'");
        $stats['total_students'] = $result->fetch_assoc()['count'];

        // Total assignments
        $result = $conn->query("SELECT COUNT(*) as count FROM assignments");
        $stats['total_assignments'] = $result->fetch_assoc()['count'];

        // Total submissions
        $result = $conn->query("SELECT COUNT(*) as count FROM submissions");
        $stats['total_submissions'] = $result->fetch_assoc()['count'];

        // Graded submissions
        $result = $conn->query("SELECT COUNT(*) as count FROM submissions WHERE grade IS NOT NULL AND grade != ''");
        $stats['graded_submissions'] = $result->fetch_assoc()['count'];

        // Average grade percentage
        $result = $conn->query("SELECT AVG(
            CAST(SUBSTRING_INDEX(grade, '/', 1) AS DECIMAL) /
            CAST(SUBSTRING_INDEX(grade, '/', -1) AS DECIMAL) * 100
        ) as avg FROM submissions WHERE grade IS NOT NULL AND grade != ''");
        $stats['avg_grade'] = round($result->fetch_assoc()['avg'] ?? 0, 1);

        // Submissions this week
        $result = $conn->query("SELECT COUNT(*) as count FROM submissions WHERE submitted_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
        $stats['submissions_this_week'] = $result->fetch_assoc()['count'];

        // Late submissions count
        $result = $conn->query("SELECT COUNT(*) as count FROM submissions s
            JOIN assignments a ON s.assignment_id = a.id
            WHERE DATE(s.submitted_at) > a.due_date");
        $stats['late_submissions'] = $result->fetch_assoc()['count'];

        return $stats;
    }

    // Get student performance (for individual student)
    public function getStudentPerformance($student_id)
    {
        global $conn;

        $data = [];

        // Get all submissions with grades
        $stmt = $conn->prepare("SELECT s.*, a.title, a.subject, a.due_date
            FROM submissions s
            JOIN assignments a ON s.assignment_id = a.id
            WHERE s.student_id = ?
            ORDER BY s.submitted_at ASC");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $submissions = [];
        while ($row = $result->fetch_assoc()) {
            $submissions[] = $row;
        }
        $data['submissions'] = $submissions;

        // Calculate stats
        $total = count($submissions);
        $graded = 0;
        $totalGradePercent = 0;
        $subjectGrades = [];

        foreach ($submissions as $sub) {
            if (!empty($sub['grade'])) {
                $graded++;
                $parts = explode('/', $sub['grade']);
                if (count($parts) == 2 && $parts[1] > 0) {
                    $percent = ($parts[0] / $parts[1]) * 100;
                    $totalGradePercent += $percent;

                    if (!isset($subjectGrades[$sub['subject']])) {
                        $subjectGrades[$sub['subject']] = ['total' => 0, 'count' => 0];
                    }
                    $subjectGrades[$sub['subject']]['total'] += $percent;
                    $subjectGrades[$sub['subject']]['count']++;
                }
            }
        }

        $data['total_submissions'] = $total;
        $data['graded_submissions'] = $graded;
        $data['avg_grade'] = $graded > 0 ? round($totalGradePercent / $graded, 1) : 0;

        // Subject averages
        $subjectAvg = [];
        foreach ($subjectGrades as $subject => $grades) {
            $subjectAvg[$subject] = round($grades['total'] / $grades['count'], 1);
        }
        $data['subject_grades'] = $subjectAvg;

        // Get pending assignments count
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM assignments a
            WHERE a.id NOT IN (SELECT assignment_id FROM submissions WHERE student_id = ?)
            AND a.due_date >= CURDATE()");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $data['pending_assignments'] = $stmt->get_result()->fetch_assoc()['count'];

        // Get total assignments
        $result = $conn->query("SELECT COUNT(*) as count FROM assignments");
        $data['total_assignments'] = $result->fetch_assoc()['count'];

        return $data;
    }

    // Get grade trend over time for a student
    public function getStudentGradeTrend($student_id)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT s.submitted_at, s.grade, a.title
            FROM submissions s
            JOIN assignments a ON s.assignment_id = a.id
            WHERE s.student_id = ? AND s.grade IS NOT NULL AND s.grade != ''
            ORDER BY s.submitted_at ASC");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $parts = explode('/', $row['grade']);
            if (count($parts) == 2 && $parts[1] > 0) {
                $data[] = [
                    'date' => $row['submitted_at'],
                    'title' => $row['title'],
                    'grade' => round(($parts[0] / $parts[1]) * 100, 1)
                ];
            }
        }
        return $data;
    }

    // Get all submissions for export
    public function getAllSubmissionsForExport()
    {
        global $conn;
        $query = "SELECT u.full_name as student_name, u.email, a.title as assignment_title,
            a.subject, s.submitted_at, s.grade, s.feedback,
            CASE WHEN DATE(s.submitted_at) > a.due_date THEN 'Late' ELSE 'On Time' END as status
        FROM submissions s
        JOIN users u ON s.student_id = u.id
        JOIN assignments a ON s.assignment_id = a.id
        ORDER BY s.submitted_at DESC";

        $result = $conn->query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    // Get submissions for plagiarism check (text files)
    public function getSubmissionsForPlagiarism($assignment_id)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT s.id, s.file, s.comment, s.student_id, u.full_name
            FROM submissions s
            JOIN users u ON s.student_id = u.id
            WHERE s.assignment_id = ?");
        $stmt->bind_param("i", $assignment_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    // Get weekly submission pattern (by day of week)
    public function getWeeklyPattern()
    {
        global $conn;
        $query = "SELECT DAYNAME(submitted_at) as day_name,
            DAYOFWEEK(submitted_at) as day_num,
            COUNT(*) as count
        FROM submissions
        GROUP BY day_name, day_num
        ORDER BY day_num";

        $result = $conn->query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
}
