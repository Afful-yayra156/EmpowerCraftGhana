<?php
function fetchAllServices($conn) {
    $sql = "SELECT s.service_id, s.title, s.price, si.image_path 
    FROM services s 
    LEFT JOIN service_images si ON s.service_id = si.service_id 
    GROUP BY s.service_id";  // Fetch one image per service (if available)



    $result = $conn->query($sql);
    $services = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $services[] = $row;
        }
    }

    return $services;
}
?>
