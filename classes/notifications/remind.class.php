<?php

require_once __DIR__ . '/../connection.class.php';


class Remind extends Connection
{
    public function getNomorByNpk($evt_id)
    {
        // Query pertama: Ambil NPK dari database kyb_training_center
        $statement1 = $this->pdo->prepare(
            'SELECT NPK FROM event_participants WHERE EVT_ID = :evt_id
            AND APPROVED = 1 
            AND APPROVED_DEPT = 1'
        );
        $statement1->bindValue(':evt_id', $evt_id);
        $statement1->execute();

        if ($statement1->rowCount() == 0) {
            return false; // Tidak ada NPK yang cocok
        }

        $npkList = $statement1->fetchAll(PDO::FETCH_COLUMN); // Ambil daftar NPK

        // Query kedua: Ambil no_hp dari database isd berdasarkan NPK yang didapat
        $placeholders = implode(',', array_fill(0, count($npkList), '?'));
        $statement2 = $this->pdo3->prepare("SELECT no_hp FROM hp WHERE npk IN ($placeholders)");
        $statement2->execute($npkList);

        if ($statement2->rowCount() == 0) {
            return false; // Tidak ada no_hp yang cocok
        }

        return $statement2->fetchAll(PDO::FETCH_ASSOC); // Mengembalikan daftar no_hp
    }

    protected function createNewReminder($message, $no_hp, $send_date)
    {
        $conn = new Connection();
        $stmt = $conn->pdo->prepare("INSERT INTO reminders (MESSAGE, NO_HP, SEND_DATE) VALUES (:message, :no_hp, :send_date)");

        $stmt->bindValue('message', $message);
        $stmt->bindValue('no_hp', $no_hp);
        $stmt->bindValue('send_date', $send_date);

        $stmt->execute();

        return true;
    }

    protected function getAllEvents($evt_id, $t_id, $loc_id, $location, $training, $start_date, $end_date, $start_time, $end_time)
    {

        $connection = new Connection();
        $statement = $connection->pdo->prepare("SELECT 
          events.EVT_ID,
          trainings.T_ID,
          locations.LOC_ID,
          locations.LOCATION,
          trainings.TRAINING, 
          events.START_DATE, 
          events.END_DATE, 
          events.START_TIME, 
          events.END_TIME
      FROM 
          events 
      INNER JOIN 
          trainings ON events.T_ID = trainings.T_ID
      INNER JOIN 
          locations ON events.LOC_ID = locations.LOC_ID 
      WHERE 
         (:evt_id IS NULL OR events.EVT_ID = :evt_id)
          AND (:t_id IS NULL OR trainings.T_ID = :t_id)
          AND (:loc_id IS NULL OR locations.LOC_ID = :loc_id)
          AND (:location IS NULL OR locations.LOCATION = :location)
          AND (:training IS NULL OR trainings.TRAINING = :training)
          AND (:start_date IS NULL OR events.START_DATE >= :start_date)
          AND (:end_date IS NULL OR events.END_DATE <= :end_date)
          AND (:start_time IS NULL OR events.START_TIME >= :start_time)
          AND (:end_time IS NULL OR events.END_TIME <= :end_time)
      ");

        $statement->bindValue('evt_id', $evt_id);
        $statement->bindValue('t_id', $t_id);
        $statement->bindValue('loc_id', $loc_id);
        $statement->bindValue('training', $training);
        $statement->bindValue('location', $location);
        $statement->bindValue('start_date', $start_date);
        $statement->bindValue('end_date', $end_date);
        $statement->bindValue('start_time', $start_time);
        $statement->bindValue('end_time', $end_time);

        if (!$statement->execute()) {
            $statement = null;
            header("HTTP/1.1 500 Internal Server Error");
            exit();
        }

        $events = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement = null;

        return $events;
    }

    public function getAllParticipantsNames($evt_id)
    {
        $connection = new Connection();
        $sql = "SELECT u.NAME 
            FROM event_participants ep
            JOIN users u ON ep.NPK = u.NPK
            WHERE ep.EVT_ID = :evt_id
            AND ep.APPROVED_DEPT = 1
            AND ep.APPROVED = 1";

        $statement = $connection->pdo->prepare($sql);
        $statement->bindValue(":evt_id", $evt_id);

        if (!$statement->execute()) {
            header("HTTP/1.1 500 Internal Server Error");
            exit();
        }

        return $statement->fetchAll(PDO::FETCH_COLUMN); // Mengembalikan array nama saja
    }
}
