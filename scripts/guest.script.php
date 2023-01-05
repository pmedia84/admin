<?php
if ($_POST['action'] == "edit") { //check if the action type of edit has been set in the post request
        include("../connect.php");
        //determine variables
        $guest_id = $_POST['guest_id'];
        $guest_fname= mysqli_real_escape_string($db, $_POST['guest_fname']);
        $guest_sname= mysqli_real_escape_string($db, $_POST['guest_sname']);
        $guest_email= mysqli_real_escape_string($db, $_POST['guest_email']);
        $guest_address= mysqli_real_escape_string($db, $_POST['guest_address']);
        $guest_postcode= mysqli_real_escape_string($db, $_POST['guest_postcode']);
        $guest_extra_invites= mysqli_real_escape_string($db, $_POST['guest_extra_invites']);
        //Update guest
        $guest = $db->prepare('UPDATE guest_list SET guest_fname=?, guest_sname=?, guest_email=?, guest_address=?, guest_postcode=?,guest_extra_invites=?  WHERE guest_id =?');
        $guest->bind_param('sssssii',$guest_fname, $guest_sname, $guest_email, $guest_address, $guest_postcode, $guest_extra_invites, $guest_id);
        $guest->execute();
        $guest->close();

        $response = '<div class="form-response"><p>Article saved.</p></div>';
    }
    ?>