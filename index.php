<?php
    /*******************************************************************
    This is a skeleton TTC APP.  There is no styling, Javascript, 
    or any security feature in this APP; customization should be easy.
    You can download the TTC csv files from this URL:
    http://opendata.toronto.ca/TTC/routes/OpenData_TTC_Schedules.zip
    
    This App will create the database and tables, and import the csv
    files to their respective tables.
    https://github.com/r2tigerwolf/ttc_dump
    ********************************************************************/
?>
<?php 
    include("db.class.php");
?>
    
    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST" >
        <input type="text" name="route_name" />
        <input type="submit" value="Find Bus" />
    </form>

<?php 
    $conn = new Connect();
    $busConn = $conn->dbconnect();
    
    $bus = new Bus; 

    if(isset($_POST['route_name'])) {
        $sqlArray = array('conn' => $busConn, 'rows' => 'route_id, route_long_name, route_short_name', 'table' => 'routes', 'join' => '', 'where' => 'route_long_name like "%'.$_POST["route_name"].'%"', 'order' => 'ORDER BY route_short_name DESC', 'limit' => '');
        $routeResult = $bus->select($sqlArray); 
		
        echo '<ul id="route">';
        foreach($routeResult  as $key => $val) {
            echo '<li><a href = "' .$_SERVER['PHP_SELF'] . '?route=' . $val['route_id'] . '&routename=' . $val['route_long_name'] . '">' . $val['route_long_name'] . ' ' . $val['route_short_name'] . '</a></li>';  
        }
        echo '</ul>';
    }
    
    if(isset($_GET['route'])) {
        $rows = 'r.route_long_name, r.route_short_name, 
                trips.route_id, trips.trip_id, trips.trip_headsign, 
                st.stop_id, st.arrival_time, st.departure_time,s.stop_name, s.stop_lat, s.stop_lon';
                
        $join = 'LEFT JOIN routes as r ON r.route_id = trips.route_id 
                LEFT JOIN stop_times as st ON trips.trip_id=st.trip_id 
                LEFT JOIN stops as s ON st.stop_id = s.stop_id';
    
        $sqlArray = array('conn' => $busConn, 'rows' => $rows, 'table' => 'trips', 'join' => $join, 'where' => 'r.route_id = "'.$_GET['route'].'"', 'order' => '', 'limit' => '');
        $tripsResult = $bus->select($sqlArray);

        echo '<ul id="trips">';
        foreach($tripsResult as $key => $val) {
            echo '<li>';
            echo $val['route_long_name']. ', Bus Name: ' . $val['trip_headsign'] . ', Arrive at: ' . 
            date("g:i A", strtotime($val['arrival_time'])) . ', Depart at: ' . 
            date("g:i A", strtotime($val['departure_time'])) . ', Coordinates: ' . 
            $val['stop_lat'] . ' ' . $val['stop_lon'] . '<br />';
            echo $val['stop_name'];
            echo '<br /><br />';
            echo '</li>';  
        }
        echo '</ul>';
    }
?>