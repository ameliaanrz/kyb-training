<?php
require_once __DIR__ . '/../connection.class.php';
class Overview extends Connection
{
    protected function getAllOvr(){

        $conn= new Connection();
        $statement =$conn->pdo->prepare('SELECT * FROM overview_user');

        if (!$statement->execute()) {
            header("HTTP/1.1 500 Internal Server Error");
            $stmt = null;
            exit();
        }

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement = null;
        return $result;
    }

    protected function updateOvr($id_overview,$img_slider,$img_profile1,$title_profile1,$desc_profile1,$img_profile2,$title_profile2,$desc_profile2){
    $conn = new Connection();
    $statement = $conn->pdo->prepare('UPDATE overview_user SET img_slider = :img_slider,img_profile_1=:img_profile1,title_profile_1=:title_profile1,desc_profile_1=:desc_profile1,img_profile_2=:img_profile2,title_profile_2=:title_profile2,desc_profile_2=:desc_profile2 WHERE id_overview=:id_overview');
    $statement->bindParam('id_overview', $id_overview);
    $statement->bindParam('img_slider', $img_slider);
    $statement->bindParam('img_profile1', $img_profile1);
    $statement->bindParam('title_profile1', $title_profile1);
    $statement->bindParam('desc_profile1', $desc_profile1);
    $statement->bindParam('img_profile2', $img_profile2);
    $statement->bindParam('title_profile2', $title_profile2);
    $statement->bindParam('desc_profile2', $desc_profile2);

    if (!$statement->execute()) {
        header("HTTP/1.1 500 Internal Server Error");
        $stmt = null;
        header("Location: /update_content.php?error=stmterror");
        return false;
    }
    $statement = null;
    return true;
    }
}
?>