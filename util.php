<?
/*
 * Utility routines for MySQL.
 */
// import_request_variables('g,p,c'); //lpw

class MySQL_class {
    var $db, $id, $result, $rows, $data, $a_rows;
    var $user, $pass, $host;

    /* Make sure you change the USERNAME and PASSWORD to your name and
     * password for the DB
     */

    function Setup ($user, $pass) {
        $this->user = $user;
        $this->pass = $pass;
    }

    function Create ($db) {
        if (!$this->user) {
            $this->user = "bizcardstodaynew";
        }
        if (!$this->pass) {
            $this->pass = "Henna4!hear";
        }
        $this->db = $db;
        $this->id = @mysql_pconnect("bizcardstodaynew.db.14040886.016.hostedresource.net", "bizcardstodaynew", "Henna4!hear") or
            $this->MySQL_ErrorMsg("Unable to connect to MySQL server: $this->host : '$SERVER_NAME'");
        $this->selectdb($db);
        $this->failed=false;
    }

    function SelectDB ($db) {
        @mysql_select_db($db, $this->id) or
            $this->MySQL_ErrorMsg ("Unable to select database: $db");
    }

    # Use this function is the query will return multiple rows.  Use the Fetch
    # routine to loop through those rows.
    function Query ($query) {
        $this->result = @mysql_query($query, $this->id) or
                     $this->MySQL_ErrorMsg ("Unable to perform query: $query");
        $this->rows = @mysql_num_rows($this->result);
        $this->a_rows = @mysql_affected_rows($this->id);
        $this->fields= @mysql_num_fields($this->result);
    }

    function rowsAffected ()
    {
    	return($this->a_rows);
    }

    # Use this function if the query will only return a
    # single data element.
    function QueryItem ($query) {
        $this->result = @mysql_query($query, $this->id) or
                    $this->MySQL_ErrorMsg ("Unable to perform query: $query");
        $this->rows = @mysql_num_rows($this->result);
        $this->a_rows = @mysql_affected_rows($this->id);
        $this->data = @mysql_fetch_array($this->result);// or
        //            $this->MySQL_ErrorMsg ("Unable to fetch data from query: $query");
        return($this->data[0]);
    }

    # This function is useful if the query will only return a
    # single row.
    function QueryRow ($query) {
// exit("-$query-");
        $this->result = @mysql_query($query, $this->id) or
                    $this->MySQL_ErrorMsg ("Unable to perform query: $query");
        $this->rows = @mysql_num_rows($this->result);
        $this->a_rows = @mysql_affected_rows($this->id);
        $this->data = @mysql_fetch_array($this->result); //or
        //            $this->MySQL_ErrorMsg ("Unable to fetch data from query: $query");
        return($this->data);
    }

    function Fetch ($row) {
        @mysql_data_seek($this->result, $row) or
                    $this->MySQL_ErrorMsg ("Unable to seek data row: $row");
        $this->data = @mysql_fetch_array($this->result) or
                    $this->MySQL_ErrorMsg ("Unable to fetch row: $row");
    }

    function Insert ($query) {
        $this->result = @mysql_query($query, $this->id) or
                    $this->MySQL_ErrorMsg ("Unable to perform insert: $query");
        $this->a_rows = @mysql_affected_rows($this->id);
    }

    function Update ($query) {
        $this->result = @mysql_query($query, $this->id) or
                    $this->MySQL_ErrorMsg ("Unable to perform update: $query");
        $this->a_rows = @mysql_affected_rows($this->id);
    }

    function Delete ($query) {
        $this->result = @mysql_query($query, $this->id) or
                    $this->MySQL_ErrorMsg ("Unable to perform Delete: $query");
        $this->a_rows = @mysql_affected_rows($this->id);
    }

	function MySQL_ErrorMsg ($msg) {
		 # Close out a bunch of HTML constructs which might prevent
		 # the HTML page from displaying the error text.
		 echo("</ul></dl></ol>\n");
		 echo("</table></script>\n");

		 # Display the error message
		 $text  = "<font color=\"#ff0000\" size=+2><p>Error: $msg :";
		 $text .= mysql_error();
		 $text .= "</font>\n";
		 $this->failed=true;
		 die($text);

	}
}

/* ********************************************************************
 * MySQL_ErrorMsg
 *
 * Print out an MySQL error message
 *
 */

?>

