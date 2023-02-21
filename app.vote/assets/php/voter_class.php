<?php

class Voter extends Account
{
    /** 
     * Creates account for voter. Returns wether or not 
     * it was successful.
     * 
     * @param int $school_id
     * @param string $username
     * @param string $password
     * @param string $confirm_password
     * @param string $email
     * @param string $first_name
     * @param string $last_name
     * @param string $gender
     * @param string $grade
     * @param string $birth_date
     * @return bool
     */
    public function createVoter(int $school_id, string $username, string $password, string $confirm_password, string $email, string $first_name, string $last_name, string $gender, string $grade, string $birth_date): bool 
    {
        global $db;

        $success = true;

        // Validates gender
        if (!$this -> isGenderValid($gender))
        {
            $success = false;
        }

        // Validates grade
        if (!$this -> isGradeValid($grade))
        {
            $success = false;
        }

        // Validates birth date
        if (!$this -> isBirthdateValid($birth_date))
        {
            $success = false;
        }

        if ($success)
        {
            $success = $this -> createAccount(
                $school_id, 
                $username, 
                $password, 
                $confirm_password, 
                $email, 
                $first_name, 
                $last_name, 
                2 //this was previously set to school admin, which is obviously a BAD IDEA
            );
        
            // Checks if it successfully created an account
            if ($success)
            {
                // Inserts voter information into database
                $insert_voter = $db -> prepare("
                    INSERT INTO voters 
                    (user_id, school_id, first_name, last_name, gender, grade, birth_date) 
                    VALUES 
                    (:user_id, :school_id, :first_name, :last_name, :gender, :grade, :birth_date)
                ");

                $insert_voter -> execute([
                    ":user_id" => $this -> user_id,
                    ":school_id" => $this -> school_id, 
                    ":first_name" => $first_name,
                    ":last_name" => $last_name,
                    ":gender" => $gender,
                    ":grade" => $grade,
                    ":birth_date" => $birth_date,
                ]);

                return true;
            } 
        }

        return false;
    }

    /** 
     * Returns wether a givin birth date is valid or not.
     * 
     * @param string $birth_date
     * @return bool
     */
    public function isBirthdateValid(string $birth_date): bool
    {
        $valid = true;

        $valid_date = "/([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))/";

        // Checks wether the birth date is formated correctly
        if (!preg_match($valid_date, $birth_date))
        {
            $this -> errors["birth-date"] = "Invalid birth date format";
            $valid = false;
        }

        return $valid;
    }

    /** 
     * Returns wether a givin grade is valid or not.
     * 
     * @param string $grade
     * @return bool
     */
    public function isGradeValid(string $grade): bool 
    {
        $valid = true;

        $grade = intval($grade);

        // Checks wether grade is between the givin range
        if (!(7 <= $grade && $grade <= 12))
        {
            $this -> errors["grade"] = "Invalid grade";
            $valid = false;
        }

        return $valid;
    }

    /** 
     * Returns wether a givin gender is valid or not.
     * 
     * @param string $gender
     * @return bool
     */
    public function isGenderValid(string $gender): bool
    {
        $valid = true;

        $valid_genders = ["Male", "Female", "Other"];

        // Checks wether the gender is in the list of valid genders or not
        if (!in_array($gender, $valid_genders, True))
        {
            $this -> errors["gender"] = "Invalid gender";
            $valid = false;
        }

        return $valid;
    }

    /** 
     * Returns a specified column from the database. If no column is 
     * specified it returns all attributes.
     * 
     * @param string $column
     * @return mixed
     */
    public function getVoterInfo(string $column = NULL)
    {
        global $db;

        // Gets voter information
        $get_voter = $db -> prepare("
            SELECT * FROM voters WHERE user_id=:user_id LIMIT 1;
        "); 

        $get_voter -> execute([
            ":user_id" => $this -> user_id,
        ]);
         
        $voter_info = $get_voter -> fetch(PDO::FETCH_ASSOC);

        // Checks if an column was specified
        if ($column == NULL)
        {
            // Returns all attributes
            return $voter_info;
        }

        // Checks if the column is in the database
        else if (isset($voter_info[$column]))
        {
            // Returns the column specified
            return $voter_info[$column];
        }

        // Invalid column
        return NULL;
    }
}

?>