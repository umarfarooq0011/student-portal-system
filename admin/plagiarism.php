<?php
require_once '../auth/authsession.php';
require_once '../admin_includes/header.php';
require_once '../admin_includes/sidebar.php';
require_once '../admin_includes/navbar.php';
require_once '../models/Analytics.php';
require_once '../models/Assignment.php';

$analytics = new Analytics();
$assignmentModel = new Assignment();
$conn = $GLOBALS['conn'];

// Get all assignments
$assignments = $assignmentModel->getAll();
$assignmentList = [];
while ($row = mysqli_fetch_assoc($assignments)) {
    $assignmentList[] = $row;
}

$selectedAssignment = isset($_GET['assignment_id']) ? intval($_GET['assignment_id']) : null;
$plagiarismResults = [];
$submissionsData = [];

if ($selectedAssignment) {
    $submissions = $analytics->getSubmissionsForPlagiarism($selectedAssignment);
    $submissionsData = $submissions;

    // Read file contents, hashes, and comments
    $contents = [];
    foreach ($submissions as $sub) {
        $text = '';
        $fileHash = '';
        $fileSize = 0;
        $fileExists = false;

        // Add comment text
        if (!empty($sub['comment'])) {
            $text .= $sub['comment'] . ' ';
        }

        // Check file
        $filePath = '../assignments/uploads/' . $sub['file'];
        if (file_exists($filePath)) {
            $fileExists = true;
            $fileSize = filesize($filePath);
            $fileHash = md5_file($filePath); // Get file hash for exact match detection

            // Try to read text from file if it's a text file
            $ext = strtolower(pathinfo($sub['file'], PATHINFO_EXTENSION));
            $textExtensions = ['txt', 'md', 'csv', 'json', 'xml', 'html', 'css', 'js', 'php', 'py', 'java', 'c', 'cpp', 'h', 'sql', 'log'];
            if (in_array($ext, $textExtensions)) {
                $fileContent = @file_get_contents($filePath);
                if ($fileContent !== false) {
                    $text .= $fileContent;
                }
            }
        }

        $contents[$sub['id']] = [
            'id' => $sub['id'],
            'student_id' => $sub['student_id'],
            'student_name' => $sub['full_name'],
            'file' => $sub['file'],
            'text' => $text,
            'file_hash' => $fileHash,
            'file_size' => $fileSize,
            'file_exists' => $fileExists
        ];
    }

    // Compare all pairs
    $compared = [];
    foreach ($contents as $id1 => $data1) {
        foreach ($contents as $id2 => $data2) {
            if ($id1 >= $id2) continue; // Skip self and duplicates

            $pairKey = min($id1, $id2) . '-' . max($id1, $id2);
            if (isset($compared[$pairKey])) continue;
            $compared[$pairKey] = true;

            $similarity = 0;
            $matchType = '';
            $text1Preview = '';
            $text2Preview = '';

            // Method 1: Check for IDENTICAL FILES using hash
            if ($data1['file_exists'] && $data2['file_exists'] &&
                !empty($data1['file_hash']) && !empty($data2['file_hash']) &&
                $data1['file_hash'] === $data2['file_hash']) {

                $similarity = 100;
                $matchType = 'Identical File';
                $text1Preview = 'File hash: ' . substr($data1['file_hash'], 0, 16) . '... (Size: ' . round($data1['file_size']/1024, 2) . ' KB)';
                $text2Preview = 'File hash: ' . substr($data2['file_hash'], 0, 16) . '... (Size: ' . round($data2['file_size']/1024, 2) . ' KB)';
            }
            // Method 2: Check text content similarity
            elseif (strlen($data1['text']) >= 20 && strlen($data2['text']) >= 20) {
                similar_text(strtolower($data1['text']), strtolower($data2['text']), $similarity);
                $similarity = round($similarity, 1);
                $matchType = 'Text Similarity';
                $text1Preview = substr($data1['text'], 0, 200);
                $text2Preview = substr($data2['text'], 0, 200);
            }
            // Method 3: Check if same file size (potential copy with different name)
            elseif ($data1['file_exists'] && $data2['file_exists'] &&
                    $data1['file_size'] > 1000 && $data1['file_size'] === $data2['file_size']) {

                // Same size files - calculate partial similarity based on first/last bytes
                $file1Content = @file_get_contents('../assignments/uploads/' . $data1['file'], false, null, 0, 1000);
                $file2Content = @file_get_contents('../assignments/uploads/' . $data2['file'], false, null, 0, 1000);

                if ($file1Content && $file2Content) {
                    similar_text($file1Content, $file2Content, $similarity);
                    $similarity = round($similarity, 1);
                    if ($similarity >= 30) {
                        $matchType = 'Similar File Structure';
                        $text1Preview = 'File size: ' . round($data1['file_size']/1024, 2) . ' KB';
                        $text2Preview = 'File size: ' . round($data2['file_size']/1024, 2) . ' KB';
                    }
                }
            }

            // Add to results if similarity is >= 30%
            if ($similarity >= 30) {
                $plagiarismResults[] = [
                    'student1' => $data1['student_name'],
                    'student2' => $data2['student_name'],
                    'file1' => $data1['file'],
                    'file2' => $data2['file'],
                    'similarity' => $similarity,
                    'match_type' => $matchType,
                    'text1_preview' => $text1Preview,
                    'text2_preview' => $text2Preview
                ];
            }
        }
    }

    // Sort by similarity (highest first)
    usort($plagiarismResults, function($a, $b) {
        return $b['similarity'] - $a['similarity'];
    });
}
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Plagiarism Detection</h1>
        <p class="text-slate-500 text-sm mt-1">Compare student submissions for similarity</p>
    </div>
    <div class="flex items-center gap-2 px-4 py-2 rounded-xl bg-rose-50 border border-rose-200">
        <i class="bi bi-shield-exclamation text-rose-600"></i>
        <span class="text-sm font-medium text-rose-700">Academic Integrity Tool</span>
    </div>
</div>

<!-- Assignment Selection -->
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Select Assignment to Check</h3>
                <p class="text-xs text-slate-500 mt-0.5">Choose an assignment to compare submissions</p>
            </div>
            <div class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center">
                <i class="bi bi-file-earmark-text text-indigo-600"></i>
            </div>
        </div>
    </div>
    <div class="p-6">
        <form method="GET" class="flex flex-col sm:flex-row gap-4">
            <select name="assignment_id" class="flex-1 px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all">
                <option value="">-- Select an Assignment --</option>
                <?php foreach ($assignmentList as $assignment): ?>
                <option value="<?php echo $assignment['id']; ?>" <?php echo $selectedAssignment == $assignment['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($assignment['title']); ?> (<?php echo htmlspecialchars($assignment['subject']); ?>)
                </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition-colors font-medium flex items-center gap-2">
                <i class="bi bi-search"></i> Check for Plagiarism
            </button>
        </form>
    </div>
</div>

<?php if ($selectedAssignment): ?>
<!-- Results Section -->
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Similarity Results</h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    <?php echo count($submissionsData); ?> submissions analyzed |
                    <?php echo count($plagiarismResults); ?> potential matches found
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span class="flex items-center gap-1 text-xs">
                    <span class="w-3 h-3 rounded-full bg-rose-500"></span> High (70%+)
                </span>
                <span class="flex items-center gap-1 text-xs">
                    <span class="w-3 h-3 rounded-full bg-amber-500"></span> Medium (50-70%)
                </span>
                <span class="flex items-center gap-1 text-xs">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span> Low (30-50%)
                </span>
            </div>
        </div>
    </div>

    <?php if (empty($plagiarismResults)): ?>
    <div class="p-12 text-center">
        <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-4">
            <i class="bi bi-check-circle text-3xl text-emerald-600"></i>
        </div>
        <h4 class="text-lg font-semibold text-slate-800 mb-2">No Significant Matches Found</h4>
        <p class="text-slate-500 text-sm">All submissions appear to be unique (below 30% similarity threshold).</p>
    </div>
    <?php else: ?>
    <div class="divide-y divide-slate-100">
        <?php foreach ($plagiarismResults as $result): ?>
        <?php
            $severity = $result['similarity'] >= 70 ? 'rose' : ($result['similarity'] >= 50 ? 'amber' : 'emerald');
            $severityText = $result['similarity'] >= 70 ? 'High Risk' : ($result['similarity'] >= 50 ? 'Medium Risk' : 'Low Risk');
            $matchType = $result['match_type'] ?? 'Text Similarity';
            $isIdentical = ($matchType === 'Identical File');
        ?>
        <div class="p-6 hover:bg-slate-50 transition-colors <?php echo $isIdentical ? 'bg-rose-50/50' : ''; ?>">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-4">
                <div class="flex items-center gap-4">
                    <!-- Similarity Badge -->
                    <div class="w-16 h-16 rounded-xl bg-<?php echo $severity; ?>-100 flex flex-col items-center justify-center <?php echo $isIdentical ? 'ring-2 ring-rose-400' : ''; ?>">
                        <span class="text-xl font-bold text-<?php echo $severity; ?>-600"><?php echo $result['similarity']; ?>%</span>
                        <span class="text-xs text-<?php echo $severity; ?>-500">Match</span>
                    </div>

                    <!-- Students Info -->
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-3 py-1 rounded-lg bg-slate-100 text-slate-700 font-medium text-sm">
                                <?php echo htmlspecialchars($result['student1']); ?>
                            </span>
                            <i class="bi bi-arrow-left-right text-slate-400"></i>
                            <span class="px-3 py-1 rounded-lg bg-slate-100 text-slate-700 font-medium text-sm">
                                <?php echo htmlspecialchars($result['student2']); ?>
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-2">
                            Files: <?php echo htmlspecialchars($result['file1']); ?> vs <?php echo htmlspecialchars($result['file2']); ?>
                        </p>
                    </div>
                </div>

                <div class="flex flex-col items-end gap-2">
                    <span class="px-3 py-1 rounded-lg bg-<?php echo $severity; ?>-100 text-<?php echo $severity; ?>-700 font-medium text-sm">
                        <?php echo $severityText; ?>
                    </span>
                    <span class="px-2 py-0.5 rounded text-xs font-medium <?php echo $isIdentical ? 'bg-rose-600 text-white' : 'bg-slate-200 text-slate-600'; ?>">
                        <i class="bi <?php echo $isIdentical ? 'bi-files' : 'bi-file-text'; ?> mr-1"></i>
                        <?php echo htmlspecialchars($matchType); ?>
                    </span>
                </div>
            </div>

            <!-- Content Preview -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 rounded-lg bg-slate-50 border border-slate-200">
                    <p class="text-xs font-semibold text-slate-600 mb-2"><?php echo htmlspecialchars($result['student1']); ?>'s Submission:</p>
                    <p class="text-sm text-slate-700 line-clamp-3"><?php echo htmlspecialchars($result['text1_preview']); ?><?php echo strlen($result['text1_preview']) > 0 && $matchType === 'Text Similarity' ? '...' : ''; ?></p>
                </div>
                <div class="p-4 rounded-lg bg-slate-50 border border-slate-200">
                    <p class="text-xs font-semibold text-slate-600 mb-2"><?php echo htmlspecialchars($result['student2']); ?>'s Submission:</p>
                    <p class="text-sm text-slate-700 line-clamp-3"><?php echo htmlspecialchars($result['text2_preview']); ?><?php echo strlen($result['text2_preview']) > 0 && $matchType === 'Text Similarity' ? '...' : ''; ?></p>
                </div>
            </div>

            <?php if ($isIdentical): ?>
            <div class="mt-4 p-3 rounded-lg bg-rose-100 border border-rose-200">
                <p class="text-sm text-rose-800 font-medium">
                    <i class="bi bi-exclamation-triangle-fill mr-1"></i>
                    These students submitted the exact same file! This requires immediate review.
                </p>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Information Box -->
<div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl border border-indigo-200 overflow-hidden">
    <div class="p-6">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                <i class="bi bi-info-circle text-xl text-indigo-600"></i>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2">How Plagiarism Detection Works</h4>
                <ul class="text-sm text-slate-600 space-y-2">
                    <li class="flex items-start gap-2">
                        <i class="bi bi-1-circle-fill text-indigo-600 mt-0.5"></i>
                        <span><strong>Identical File Detection:</strong> Uses MD5 hash to detect when students submit the exact same file (100% match)</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="bi bi-2-circle-fill text-indigo-600 mt-0.5"></i>
                        <span><strong>Text Similarity:</strong> Compares text content from comments and text files (.txt, .md, code files) using similarity algorithm</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="bi bi-3-circle-fill text-indigo-600 mt-0.5"></i>
                        <span><strong>File Structure:</strong> Detects files with same size that may have similar content (PDFs, images, documents)</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="bi bi-check2 text-emerald-600 mt-0.5"></i>
                        <span>Shows matches with 30% or higher similarity for review</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="bi bi-exclamation-triangle text-amber-600 mt-0.5"></i>
                        <span><strong>Note:</strong> High similarity doesn't always mean plagiarism. Always review manually before taking action.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<?php require_once '../admin_includes/footer.php'; ?>
