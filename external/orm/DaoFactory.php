<?php
require("../config.php");

class DaoFactory
{

    static function db($conn)
    {
        if (is_array($conn)) {
            return self::sql_connect($conn);
        }

        return $conn;
    }

    static function sql_connect($sql_details)
    {
        try {
            $db = @new PDO(
                "mysql:host={$sql_details['host']};dbname={$sql_details['db']}",
                $sql_details['user'],
                $sql_details['pass'],
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
            );
        } catch (PDOException $e) {
            self::fatal(
                "An error occurred while connecting to the database. " .
                "The error reported by the server was: " . $e->getMessage()
            );
        }

        return $db;
    }

    public static function delete($conn, $table, $primary_key, $id, $columns = null)
    {
        $responseData = array();
        $db = self::db($conn);
        if($table == 'videos'){
            $v_sql = "SELECT videoPath,thumbnailPath FROM videos WHERE videoId=" . $id;
            $v_query = $db->prepare($v_sql);
            $res = $v_query->execute();
            $v_response = $v_query->fetchAll();
            $videoPath = $v_response[0]['videoPath'];
            unlink($videoPath);
            $thumbnailPath = $v_response[0]['thumbnailPath'];
            unlink($thumbnailPath);
        }
        $sql = "DELETE FROM " . $table . " WHERE " . $primary_key . " = ?";
        $query = $db->prepare($sql);
        // Execute
        try {
            $response = $query->execute(array($id));
            if ($response) {
                $responseData = array(
                    "content" => "Successfully Deleted",
                    "http_code" => 403
                );
            } else {
                $responseData = array(
                    "content" => "Something went wrong",
                    "http_code" => 500
                );
            }
        } catch (PDOException $e) {
            $responseData = array(
                "content" => self::fatal("An SQL error occurred: " . $e->getMessage()),
                "http_code" => 500
            );
        }
        return $responseData;
    }

    public static function delete_eventuser($conn, $table, $primary_key, $id, $rsvp, $columns = null)
    {
        $responseData = array();
        $db = self::db($conn);
        if($table == 'videos'){
            $v_sql = "SELECT videoPath,thumbnailPath FROM videos WHERE videoId=" . $id;
            $v_query = $db->prepare($v_sql);
            $res = $v_query->execute();
            $v_response = $v_query->fetchAll();
            $videoPath = $v_response[0]['videoPath'];
            unlink($videoPath);
            $thumbnailPath = $v_response[0]['thumbnailPath'];
            unlink($thumbnailPath);
        }

        $sql = "DELETE FROM event_users eu 
                LEFT JOIN events ev on eu.event_id = ev.id 
                WHERE eu.id = 12 AND ev.is_active = 1";



//" . $table . " WHERE " . $primary_key . " = ? AND rsvp_date=".$rsvp;
        $query = $db->prepare($sql);
        // Execute
        try {
            $response = $query->execute(array($id));
            if ($response) {
                $responseData = array(
                    "content" => "Successfully Deleted",
                    "http_code" => 403
                );
            } else {
                $responseData = array(
                    "content" => "Something went wrong",
                    "http_code" => 500
                );
            }
        } catch (PDOException $e) {
            $responseData = array(
                "content" => self::fatal("An SQL error occurred: " . $e->getMessage()),
                "http_code" => 500
            );
        }
        return $responseData;
    }

    public static function delete_is_deleted_column_update($conn, $table, $primary_key, $id, $condition = null)
    {
        $responseData = array();
        $db = self::db($conn);
        $sql = "UPDATE " . $table . " SET " . $condition . " WHERE " . $primary_key . "=" . $id;
        $query = $db->prepare($sql);
        // Execute
        try {
            $response = $query->execute(array($id));
            if ($response) {
                $responseData = array(
                    "content" => "Successfully deleted",
                    "http_code" => 403
                );
            } else {
                $responseData = array(
                    "content" => "Something went wrong",
                    "http_code" => 500
                );
            }
        } catch (PDOException $e) {
            $responseData = array(
                "content" => self::fatal("An SQL error occurred: " . $e->getMessage()),
                "http_code" => 500
            );
        }
        return $responseData;
    }

    public static function status_change($conn, $table, $primary_key, $id, $condition = null)
    {
        $responseData = array();
        $db = self::db($conn);
        $sql = "UPDATE " . $table . " SET " . $condition . " WHERE " . $primary_key . "=" . $id;
        $query = $db->prepare($sql);
        // Execute
        try {
            $response = $query->execute(array($id));
            if ($response) {
                $responseData = array(
                    "content" => "Successfully updated",
                    "http_code" => 201
                );
            } else {
                $responseData = array(
                    "content" => "Something went wrong",
                    "http_code" => 500
                );
            }
        } catch (PDOException $e) {
            $responseData = array(
                "content" => self::fatal("An SQL error occurred: " . $e->getMessage()),
                "http_code" => 500
            );
        }
        return $responseData;
    }

    public static function status_update($conn, $table, $where, $condition = null)
    {
        $responseData = array();
        $db = self::db($conn);
        $sql = "UPDATE " . $table . " SET " . $condition ." WHERE ". $where;
        $query = $db->prepare($sql);
        // Execute
        try {
            $response = $query->execute();
            if ($response) {
                $responseData = array(
                    "content" => "Successfully updated",
                    "http_code" => 201
                );
            } else {
                $responseData = array(
                    "content" => "Something went wrong",
                    "http_code" => 500
                );
            }
        } catch (PDOException $e) {
            $responseData = array(
                "content" => self::fatal("An SQL error occurred: " . $e->getMessage()),
                "http_code" => 500
            );
        }
        return $responseData;
    }

    public static function rsvp_check($conn, $table_rsvp, $primary_key_rsvp, $id_rsvp, $condition = 1)
    {
        $responseData = array();
        $db = self::db($conn);

        $sql_rsvp = "SELECT * FROM " . $table_rsvp . " WHERE ".$primary_key_rsvp ."=". $id_rsvp . " AND ".$condition;
        $query_rsvp = $db->prepare($sql_rsvp);
        $res_rsvp = $query_rsvp->execute();
        $response_rsvp = $query_rsvp->fetchAll();
        $rsvp_date = $response_rsvp[0]["$primary_key_rsvp"];

        if($rsvp_date){
            return 1;
        }else
            return 0;

    }


}