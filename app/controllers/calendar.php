<?php
// app/controllers/calendar.php - ICS endpoint and optional calendar view

declare(strict_types=1);

function ics_action(): string {
    $id = (int)($_GET['interview_id'] ?? 0);
    if ($id <= 0) { http_response_code(400); return 'Invalid interview_id'; }
    $stmt = db()->prepare("SELECT i.*, c.first_name, c.last_name, e.name AS employer
                           FROM interviews i
                           JOIN candidates c ON c.id=i.candidate_id
                           JOIN employers e ON e.id=i.employer_id
                           WHERE i.id=:id");
    $stmt->execute([':id' => $id]);
    $iv = $stmt->fetch();
    if (!$iv) { http_response_code(404); return 'Not found'; }

    // Build ICS
    $start = new DateTime($iv['scheduled_at'], new DateTimeZone('UTC'));
    $end = (clone $start)->modify('+' . (int)$iv['duration_mins'] . ' minutes');
    $uid = 'iv-' . $iv['id'] . '@ppms';
    $summary = 'Interview: ' . $iv['employer'];
    $desc = 'Interview for candidate ' . $iv['first_name'] . ' ' . $iv['last_name'];
    $location = $iv['location'] ?: ($iv['meeting_link'] ?: '');

    header('Content-Type: text/calendar; charset=utf-8');
    header('Content-Disposition: attachment; filename="interview-' . (int)$iv['id'] . '.ics"');

    $lines = [
        'BEGIN:VCALENDAR',
        'VERSION:2.0',
        'PRODID:-//PPMS//EN',
        'CALSCALE:GREGORIAN',
        'METHOD:PUBLISH',
        'BEGIN:VEVENT',
        'UID:' . $uid,
        'DTSTAMP:' . gmdate('Ymd\THis\Z'),
        'DTSTART:' . $start->format('Ymd\THis\Z'),
        'DTEND:' . $end->format('Ymd\THis\Z'),
        'SUMMARY:' . str_replace(["\r","\n"], ' ', $summary),
        'DESCRIPTION:' . str_replace(["\r","\n"], ' ', $desc),
        'LOCATION:' . str_replace(["\r","\n"], ' ', $location),
        'END:VEVENT',
        'END:VCALENDAR',
    ];

    echo implode("\r\n", $lines);
    exit;
}

function index_action(): string {
    // Simple placeholder
    return 'Calendar endpoint';
}
