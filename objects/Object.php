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
    private $table_plz = "plz";

    //user properties
    public $user_lat;
    public $user_long;

    // object properties
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
                        id, name, short_text, img_url, size, latitude, longitude
                    FROM
                        ' . $this->table. '
                    WHERE
                        latitude BETWEEN :min_lat AND :max_lat
                    AND
                        longitude BETWEEN :min_long AND :max_long';

        // prepare the query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->student=htmlspecialchars(strip_tags($this->student));

        // bind the values
        $stmt->bindParam(':min_lat', $this->user_lat - $distanceInLatitude);
        $stmt->bindParam(':max_lat', $this->user_lat + $distanceInLatitude);
        $stmt->bindParam(':min_long', $this->user_long - $distanceInLongitude);
        $stmt->bindParam(':max_long', $this->user_long + $distanceInLongitude);

        // exit if execute failed
        if(!$stmt->execute()){
            return false;
        }

        // get record details / values
        $this->multi_objects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return true;
    }

}