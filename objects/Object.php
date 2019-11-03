<?php
// 'object' object
class Object{

    //config
    private $distance_const = 111120; // m
    private $distance = 5000; // in m

    // database connection and table name
    private $conn;
    private $table = "object";
    private $table_info = "information";
    private $table_author = "author";
    private $table_town = "town";

    //user properties
    public $user_lat;
    public $user_long;

    // object properties
    public $entireObject;
    public $id;
    public $name;
    public $short_text;
    public $img_url;
    public $size;
    public $lat;
    public $long;
    public $created;

    public $author;
    public $author_id;

    public $town;
    public $town_postalcode;
    public $town_id;

    public $multi_objects;
    public $infos;   

    // constructor
    public function __construct($db){
        $this->conn = $db;
    }

    // CRUD -> Read

    // get objects in a specific area
    public function getNearObjects(){

        $latitudeInMeter = $this->distance_const;
        $meterInLatitude = 1 / $latitudeInMeter;

        $longitudeInMeter = $latitudeInMeter * cos(deg2rad($this->user_lat));
        $meterInLongitude = 1 / $longitudeInMeter;

        $distanceInLatitude = $this->distance * $meterInLatitude;
        $distanceInLongitude = $this->distance * $meterInLongitude;

        // Create Query
        $query = '  SELECT
                        o.id, o.name, o.short_text, o.img_url, o.size, o.latitude, o.longitude, t.name as town
                    FROM
                        ' . $this->table.' o, ' . $this->table_town.' t
                    WHERE
                        latitude BETWEEN :min_lat AND :max_lat
                    AND
                        longitude BETWEEN :min_long AND :max_long
                    AND
                        o.pid = t.id';

        // prepare the query
        $stmt = $this->conn->prepare($query);

        // bind the values
        $stmt->bindParam(':min_lat', round($this->user_lat - $distanceInLatitude, 6));
        $stmt->bindParam(':max_lat', round($this->user_lat + $distanceInLatitude, 6));
        $stmt->bindParam(':min_long', round($this->user_long - $distanceInLongitude, 6));
        $stmt->bindParam(':max_long', round($this->user_long + $distanceInLongitude, 6));

        // exit if execute failed
        if(!$stmt->execute()){
            return false;
        }

        // get record details / values
        $this->multi_objects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return true;
    }

    // get details of object
    public function getObjectDetails(){

        // Create Query
        $query = '  SELECT
                        *
                    FROM
                        ' . $this->table . '
                    WHERE
                        id = :object_id';

        // prepare the query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->id=htmlspecialchars(strip_tags($this->id));

        // bind the values
        $stmt->bindParam(':object_id', $this->id);

        // exit if execute failed
        if(!$stmt->execute()){
            return false;
        }

        // get record details / values
        $this->entireObject = $stmt->fetch(PDO::FETCH_ASSOC);

        return true;
    }

    // get information of object
    public function getObjectInfos(){

        // Create Query
        $query = '  SELECT
                        i.id, i.type, i.checked, i.text, i.source, i.further_info, a.firstname, a.lastname
                    FROM
                        ' . $this->table_info.' i, ' . $this->table_author.' a
                    WHERE
                        i.oid = :object_id
                    AND
                        i.aid = a.id
                    ORDER BY
                        i.created DESC';

        // prepare the query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->id=htmlspecialchars(strip_tags($this->id));

        // bind the values
        $stmt->bindParam(':object_id', $this->id);

        // exit if execute failed
        if(!$stmt->execute()){
            return false;
        }

        // get record details / values
        $this->infos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return true;
    }

}
