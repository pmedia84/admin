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
    if ($_POST['action'] == "create") { //check if the action type of create has been set in the post request
        include("../connect.php");
        //determine variables
        $guest_fname= mysqli_real_escape_string($db, $_POST['guest_fname']);
        $guest_sname= mysqli_real_escape_string($db, $_POST['guest_sname']);
        $guest_email= mysqli_real_escape_string($db, $_POST['guest_email']);
        $guest_address= mysqli_real_escape_string($db, $_POST['guest_address']);
        $guest_postcode= mysqli_real_escape_string($db, $_POST['guest_postcode']);
        $guest_extra_invites= mysqli_real_escape_string($db, $_POST['guest_extra_invites']);
        $guest_extra_invites= mysqli_real_escape_string($db, $_POST['guest_extra_invites']);
        if($_POST['guest_extra_invites']>=1){
            //if the guest has 1 or more extra invites then ad them as a group organiser
            $guest_type= "Group Organiser";
            

        }else{
            $guest_type="Member";
        }
        
        //insert guest
        $guest = $db->prepare('INSERT INTO guest_list (guest_fname, guest_sname, guest_email, guest_address, guest_postcode, guest_extra_invites, guest_type) VALUES (?,?,?,?,?,?,?)');
        $guest->bind_param('sssssis',$guest_fname, $guest_sname, $guest_email, $guest_address, $guest_postcode, $guest_extra_invites, $guest_type);
        $guest->execute();
        $guest->close();

        
        if($_POST['guest_extra_invites']>=1){
            $new_guest_id = $db->insert_id;//last id entered
            //create a guest group if the guest being added has one or more extra invites
            //set up a group name using first and last name of primary guest
            $group_name = $guest_fname.' '.$guest_sname;
            //insert into guest group tables
            $group = $db->prepare('INSERT INTO guest_groups (guest_group_name, guest_group_organiser) VALUES (?,?)');
            $group->bind_param('si',$group_name, $new_guest_id);
            $group->execute();
            $group->close();
            $new_group_id = $db->insert_id;
            //update guest list with the guest group id
            $guest = $db->prepare('UPDATE guest_list SET guest_group_id=?  WHERE guest_id =?');
            $guest->bind_param('ii',$new_group_id, $new_guest_id);
            $guest->execute();
            $guest->close();


        }
        $response = '<div class="form-response"><p>Article saved.</p></div>';
    }
