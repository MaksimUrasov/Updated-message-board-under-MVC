<?php


//a class for one old message: (am not sure how to include it into View object, so creates a separate file)

namespace App\Views;

use DateTime;

class OldMessage {

    public function __construct($email, $name, $birth_date, $message) {
        $this->generated_email = ($email !== "NULL") ? $email : "" ;
        $this->generated_name = $this->generate_link_to_email($this->generated_email,$name);
        $this->generated_age =  $this->convert_date_to_years_old($birth_date);
        $this->generated_message = ucfirst($message);  // have added ucfirst built in function to capitalize the first letter, for better appearance.
        $this->genereate_an_html();
    }

    public function generate_link_to_email($email,$name){
        if ($email){
            return "<a href='mailto:$email'>$name</a>";
        } else {
            return $name;
        }
    }

    public function convert_date_to_years_old($b_date){
        $dob = new DateTime($b_date);
        $now = new DateTime();
        $age = $now->diff($dob);
        return $age->format('%y');
    }

    public function genereate_an_html(){
        echo "<div class='container_for_one_old_message'>
        <div class='name_and_year_container'>
            <p class='old_name'>" . $this->generated_name . ",</p>  
            <p class='old_age'>" . $this->generated_age . " years.</p> 
        </div>
        <p class='old_message'>" . $this->generated_message . "</p> 
        </div>"; 
    }


};

