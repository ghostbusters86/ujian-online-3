<?php

class Reset_password_model extends CI_Model{
    function insert_data($email, $kode, $time){
        $query = $this->db->query("
            insert into reset_password (email, kode_unik, kadaluarsa) values ('$email', '$kode', '$time')
        ");
        return $query;
    }

    function cekdata($uniq){
        $query = $this->db->query("
            select * from reset_password where kode_unik = '$uniq' order by created_at desc
        ");
        return $query;
    }
}