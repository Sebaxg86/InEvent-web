<?php
// ======= Start Session and Load Database Connection =======
session_start();
require_once '../../config/database.php';

// ======= Verify Request Method =======
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ======= Retrieve Form Data =======
    $title       = $_POST['title'];
    $date        = $_POST['date'];
    $location    = $_POST['location'];
    $type        = $_POST['type'];
    $price       = $_POST['price'];
    $total_seats = $_POST['total_seats'];
    $image_url   = $_POST['image_url'];

    try {
        // ======= Insert Event Data into Database =======
        $stmt = $pdo->prepare("INSERT INTO events (title, event_date, venue, type, price, total_seats, img) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $date, $location, $type, $price, $total_seats, $image_url]);

        // ======= Get the Last Inserted Event ID =======
        $eventId = $pdo->lastInsertId();

        // ======= Check if the Event Requires Numbered Seats =======
        if ($type === 'Concert' || $type === 'Opera' ||
            $type === 'Theater' || $type === 'Conference' ||
            $type === 'Cinema') { // ======= Adjust Event Types as Needed =======
            
            $columns   = 10;           // ======= Number of Columns per Row =======
            $alphabet  = range('A', 'Z'); // ======= Generate Letters A to Z =======
            $rowIndex  = 0;            // ======= Initialize Row Index =======

            // ======= Generate Seats =======
            for ($i = 1; $i <= $total_seats; $i++) {
                // ======= Calculate Current Row =======
                if (($i - 1) % $columns == 0 && $i > 1) {
                    $rowIndex++;
                }

                // ======= Generate Row Name (A, B, ..., Z, AA, AB, ...) =======
                $rowName = '';
                $tempIndex = $rowIndex;
                while ($tempIndex >= 0) {
                    $rowName = $alphabet[$tempIndex % 26] . $rowName;
                    $tempIndex = floor($tempIndex / 26) - 1;
                }

                // ======= Create Seat Label in the Format "Row + Seat Number" =======
                $seatLabel = $rowName . (($i % $columns == 0) ? $columns : $i % $columns);

                // ======= Insert the Seat into the `seats` Table =======
                $seatStmt = $pdo->prepare("INSERT INTO seats (event_id, seat_label, is_sold) VALUES (?, ?, ?)");
                $seatStmt->execute([$eventId, $seatLabel, 0]); // ======= Initialize `is_sold` to 0 (available) =======
            }
        }

        // ======= Redirect to the Events List with Success Status =======
        header("Location: ../../events.php?view=list&success=added");
        exit();
    } catch (PDOException $e) {
        // ======= Display the PDO Error =======
        die("Database Error: " . $e->getMessage());
    }
} else {
    // ======= Redirect to Events List if Request Method is Not POST =======
    header("Location: ../events.php");
    exit();
}