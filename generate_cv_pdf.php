<?php
require_once __DIR__ . '/vendor/autoload.php';   // path to Composer autoloader

/* ------------------------------------------------------------------
   1. Pull YOUR data (however you store it – session, DB, POST …)
------------------------------------------------------------------ */
$cv = [
    'photo'        => __DIR__ . '/uploads/passport.jpg',   // physical path
    'certificates' => [
        __DIR__ . '/uploads/olevel2017.jpg',
        __DIR__ . '/uploads/olevel2020.jpg',
    ],
    'full_name'    => 'Sean Tinodiwanashe Muchesa',
    'dob'          => '09-08-2001',
    'height'       => '162 cm',
    'weight'       => '70 kg',
    'passport_no'  => 'FN647972',
    'phone'        => '+263 77 123 4567',
    'email'        => 'sean@example.com',
    'address'      => '29 Munziru, Mufakose, Harare',
    'father'       => 'Benjamin Muchesa',
    'mother'       => 'Everjoy Muchesa',
    'marital'      => 'Single',
    'languages'    => ['English', 'Shona', 'Basic Russian'],
    'skills'       => ['Microsoft Word', 'Machine Operation', 'PC skills'],
    'education'    => [
        ['year' => '2017', 'school' => 'Gutu High School', 'level' => 'Ordinary Level'],
        ['year' => '2019', 'school' => 'Glenview 2 High', 'level' => 'Advanced Level'],
    ],
    'work'         => [
        ['employer' => 'Quick Technologies', 'job' => 'Technician', 'years' => '1'],
        ['employer' => 'Varoon Factory', 'job' => 'Packaging', 'years' => '1'],
    ],
    'olevel_subjects' => [
        'Religious Studies' => 'B',
        'History'           => 'D',
        'Geography'         => 'A',
        'Economics'         => 'D',
        'Shona'             => 'D',
        'Mathematics'       => 'E',
        'Integrated Sci.'   => 'C',
        'Biology'           => 'C',
        'Agriculture'       => 'B',
        'Business Studies'  => 'D',
    ],
];

try {
    /* ------------------------------------------------------------------
       2. Fire up mPDF (A4, UTF-8, 1 cm margins)
    ------------------------------------------------------------------ */
    $mpdf = new \Mpdf\Mpdf([
        'mode'          => 'utf-8',
        'format'        => 'A4',
        'margin_left'   => 10,
        'margin_right'  => 10,
        'margin_top'    => 10,
        'margin_bottom' => 10,
    ]);

    $mpdf->SetTitle('CV - ' . $cv['full_name']);
    $mpdf->SetAuthor('Your Company Name');

    /* ------------------------------------------------------------------
       3. Build the HTML (copy of the original layout)
    ------------------------------------------------------------------ */
    $html = '
    <style>
        body { font-family: DejaVuSansCondensed; font-size: 12px; line-height: 1.4; }
        .box { border: 1px solid #000; padding: 4px; margin-bottom: 6px; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .gradeA { background: #d0ffd0; }
        .gradeB { background: #ffffd0; }
        .gradeC { background: #ffd0d0; }
        .gradeD { background: #ffdddd; }
        .gradeE { background: #ffcccc; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 4px 6px; border: 1px solid #000; }
        .photo { width: 28mm; height: 35mm; object-fit: cover; border: 1px solid #000; }
    </style>';

    /* ---------- header row: photo + name + contacts ---------- */
    $html .= '
    <table>
    <tr>
      <td rowspan="3" width="90" class="center">';
    
    // Check if photo exists before adding it
    if (file_exists($cv['photo'])) {
        $html .= '<img src="' . $cv['photo'] . '" class="photo" />';
    } else {
        $html .= '<div style="width:28mm;height:35mm;border:1px solid #000;display:flex;align-items:center;justify-content:center;">No Photo</div>';
    }
    
    $html .= '
      </td>
      <td class="bold" style="font-size:16px;">' . htmlspecialchars($cv['full_name']) . '</td>
      <td>DOB: ' . htmlspecialchars($cv['dob']) . '</td>
    </tr>
    <tr>
      <td>Height: ' . htmlspecialchars($cv['height']) . ' | Weight: ' . htmlspecialchars($cv['weight']) . '</td>
      <td>Passport: ' . htmlspecialchars($cv['passport_no']) . '</td>
    </tr>
    <tr>
      <td>Phone: ' . htmlspecialchars($cv['phone']) . '<br/>Email: ' . htmlspecialchars($cv['email']) . '</td>
      <td>Address: ' . htmlspecialchars($cv['address']) . '</td>
    </tr>
    </table>';

    /* ---------- personal details ---------- */
    $html .= '<div class="box bold">Personal Details / Личная информация</div>
    <table>
    <tr><td>Father: ' . htmlspecialchars($cv['father']) . '</td><td>Mother: ' . htmlspecialchars($cv['mother']) . '</td></tr>
    <tr><td>Marital: ' . htmlspecialchars($cv['marital']) . '</td><td>Languages: ' . htmlspecialchars(implode(', ', $cv['languages'])) . '</td></tr>
    </table>';

    /* ---------- education ---------- */
    $html .= '<div class="box bold">Education</div>
    <table>
    <tr><th>Year</th><th>School</th><th>Level</th></tr>';
    foreach ($cv['education'] as $ed) {
        $html .= "<tr><td>" . htmlspecialchars($ed['year']) . "</td><td>" . htmlspecialchars($ed['school']) . "</td><td>" . htmlspecialchars($ed['level']) . "</td></tr>";
    }
    $html .= '</table>';

    /* ---------- o-level subjects ---------- */
    $html .= '<div class="box bold">O-Level Subjects & Grades</div>
    <table>
    <tr><th>Subject</th><th>Grade</th></tr>';
    foreach ($cv['olevel_subjects'] as $subj => $gr) {
        $cls = 'grade' . $gr;
        $html .= "<tr><td>" . htmlspecialchars($subj) . "</td><td class=\"center $cls\">" . htmlspecialchars($gr) . "</td></tr>";
    }
    $html .= '</table>';

    /* ---------- work experience ---------- */
    $html .= '<div class="box bold">Work Experience</div>
    <table>
    <tr><th>Employer</th><th>Position</th><th>Years</th></tr>';
    foreach ($cv['work'] as $w) {
        $html .= "<tr><td>" . htmlspecialchars($w['employer']) . "</td><td>" . htmlspecialchars($w['job']) . "</td><td>" . htmlspecialchars($w['years']) . "</td></tr>";
    }
    $html .= '</table>';

    /* ---------- skills & interests ---------- */
    $html .= '<div class="box bold">Skills / Interests</div>
    <p>' . htmlspecialchars(implode(', ', $cv['skills'])) . '</p>';

    /* ---------- certificates gallery (bottom) ---------- */
    $html .= '<div class="box bold">Uploaded Certificates</div>';
    foreach ($cv['certificates'] as $cert) {
        if (file_exists($cert)) {
            $html .= '<img src="' . $cert . '" style="width:45%;margin:4px;"/>';
        }
    }

    /* ------------------------------------------------------------------
       4. Send PDF to browser (force download)
    ------------------------------------------------------------------ */
    $mpdf->WriteHTML($html);
    
    // Generate filename
    $filename = 'CV_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $cv['full_name']) . '.pdf';
    
    // Output as download
    $mpdf->Output($filename, 'D');

} catch (Exception $e) {
    // Handle errors gracefully
    header('Content-Type: text/html; charset=utf-8');
    echo '<h1>Error generating PDF</h1>';
    echo '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p>Please check that:</p>';
    echo '<ul>';
    echo '<li>mPDF is properly installed via Composer</li>';
    echo '<li>Image files exist in the uploads directory</li>';
    echo '<li>PHP has sufficient memory and permissions</li>';
    echo '</ul>';
}
?>