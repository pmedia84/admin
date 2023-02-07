<?php
if (isset($_GET['action'])) {
    if($_GET['action']=="load_guest_list"){
        //load guest list from the db and send back to the front page
        include("../connect.php");
        //find wedding guest list
        $guest_list_query = ('SELECT guest_list.guest_id, guest_list.guest_fname, guest_list.guest_sname, guest_list.guest_extra_invites, guest_list.guest_events, guest_list.guest_rsvp_code, guest_list.guest_rsvp_status FROM guest_list ORDER BY guest_list.guest_group_id DESC');
        $guest_list = $db->query($guest_list_query);
        $guest_list_result = $guest_list->fetch_assoc();
        $num_guests = $guest_list->num_rows;
        echo 
        '<p>Total Number Of Guests: <strong>'.$num_guests.'</strong></p>';
        echo
        '<table class="std-table">
            <tr>
                <th>Name</th>
                <th>Extra Invites</th>
                <th>RSVP Status</th>
                <th>RSVP Code</th>
                <th>Manage</th>
            </tr>'; 
    foreach($guest_list as $guest){
            
        if($guest['guest_extra_invites']>=1){
            $plus= "+".$guest['guest_extra_invites'];
        }else{
            $plus="";
        }
        echo' <tr>
        <td><a href="guest.php?guest_id='.$guest['guest_id'].'&action=view">'.$guest['guest_fname'].' '.$guest['guest_sname'].' '.$plus.'</a></td>
        <td>'.$guest['guest_extra_invites'].'</td>
        <td>'.$guest['guest_rsvp_status'].'</td>
        <td>'.$guest['guest_rsvp_code'].'</td>
        <td><div class="guest-list-actions">
                <a href="guest.php?guest_id='.$guest['guest_id'].'&action=edit"><i class="fa-solid fa-pen-to-square"></i></a>
                <a href="guest.php?guest_id='.$guest['guest_id'].'&action=delete&confirm=no"><i class="fa-solid fa-user-minus"></i></a>
            </div>
        </td>
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
               $guest_list_query = ('SELECT * FROM guest_list WHERE guest_fname LIKE "%'.$search.'%" OR guest_sname LIKE "%'.$search.'%"  ORDER BY guest_list.guest_group_id DESC');
               $guest_list = $db->query($guest_list_query);
               $guest_list_result = $guest_list->fetch_assoc();
               $num_guests = $guest_list->num_rows;
               if($num_guests ==null){
                echo '<p>Sorry, no guests match those details</p>';
               }
               if($num_guests >0){
                echo '<p>'.$num_guests.' Guests found</p>';
               }

               echo
               '<table class="std-table">
                   <tr>
                       <th>Name</th>
                       <th>Extra Invites</th>
                       <th>RSVP Status</th>
                       <th>RSVP Code</th>
                       <th>Manage</th>
                   </tr>'; 
           foreach($guest_list as $guest){
            if($guest['guest_extra_invites']>=1){
                $plus= "+".$guest['guest_extra_invites'];
            }else{
                $plus="";
            }
               echo' <tr>
               <td><a href="guest.php?guest_id='.$guest['guest_id'].'&action=view">'.$guest['guest_fname'].' '.$guest['guest_sname'].' '.$plus.'</a></td>
               <td>'.$guest['guest_extra_invites'].'</td>
               <td>'.$guest['guest_rsvp_status'].'</td>
               <td>'.$guest['guest_rsvp_code'].'</td>
               <td><div class="guest-list-actions">
                       <a href="guest.php?guest_id='.$guest['guest_id'].'&action=edit"><i class="fa-solid fa-pen-to-square"></i></a>
                       <a href="guest.php?guest_id='.$guest['guest_id'].'&action=delete&confirm=no"><i class="fa-solid fa-user-minus"></i></a>
                   </div>
               </td>
           </tr>                     
           ';}
       
           echo '</table>';
    }

    
}