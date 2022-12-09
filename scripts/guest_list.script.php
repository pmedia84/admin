<?php
if (isset($_GET['action'])) {
    if($_GET['action']=="load_guest_list"){
        //load guest list from the db and send back to the front page
        include("../connect.php");
        //find wedding guest list
        $guest_list_query = ('SELECT * FROM guest_list ORDER BY guest_sname');
        $guest_list = $db->query($guest_list_query);
        $guest_list_result = $guest_list->fetch_assoc();
        $num_guests = $guest_list->num_rows;
        echo 
        '<p>Total Number Of Guests '.$num_guests.'</p>';
        echo
        '<table class="std-table">
            <tr>
                <th>Name</th>
                <th>Attending</th>
                <th>RSVP Status</th>
            </tr>'; 
    foreach($guest_list as $guest){
        echo' <tr>
        <td><a href="">'.$guest['guest_fname'].' '.$guest['guest_sname'].' +1</a></td>
        <td>Wedding Ceremony, reception, Evening do</td>
        <td>Not Replied</td>
    </tr>                   
    ';}

    echo '</table>';
    }


        
}
if (isset($_POST['action'])) {
    //load guest list based on the search bar

    if($_POST['action']=="guest_search"){
        include("../connect.php");
        $search = mysqli_real_escape_string($db, $_POST['search']);
               //load guest list from the db and send back to the front page
               
               //find wedding guest list
               $guest_list_query = ('SELECT * FROM guest_list WHERE guest_fname LIKE "%'.$search.'%" OR guest_sname LIKE "%'.$search.'%"  ORDER BY guest_sname');
               $guest_list = $db->query($guest_list_query);
               $guest_list_result = $guest_list->fetch_assoc();
               $num_guests = $guest_list->num_rows;
               if($num_guests ==null){
                echo '<p>Sorry, no guests match those details</p>';
               }
               if($num_guests >0){
                echo '<p>'.$num_guests.' Guests found matching '.$search.'</p>';
               }

               echo
               '<table class="std-table">
                   <tr>
                       <th>Name</th>
                       <th>Attending</th>
                       <th>RSVP Status</th>
                   </tr>'; 
           foreach($guest_list as $guest){
               echo' <tr>
               <td><a href="">'.$guest['guest_fname'].' '.$guest['guest_sname'].' +1</a></td>
               <td>Wedding Ceremony, reception, Evening do</td>
               <td>Not Replied</td>
           </tr>                   
           ';}
       
           echo '</table>';
    }
}