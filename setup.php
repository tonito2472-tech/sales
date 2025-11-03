<?
  require 'admin/cp-includes/inc-confdata.php';
  require 'admin/cp-includes/inc-branding.php';
  require 'admin/cp-includes/inc-functions.php';

  $page_titl = 'Setup Wizard';
  $page_desc = '';
  $page_keys = '';
  $page_path = 'admin/';
  require 'admin/cp-includes/inc-wheader.php';
  $dirset = array ('images', 'enlarge', 'thumbs', 'temp', 'admin/cp-thumbs');
  $confdata = 'admin/cp-includes/inc-confdata.php';
  $makes = 'Acura
Aston Martin
Audi
Bentley
BMW
Buick
Cadillac
Chevrolet
Chrysler
Dodge
Ferrari
Ford
GMC
Honda
Hummer
Hyundai
Infiniti
Isuzu
Jaguar
Jeep
Kia
Lamborghini
Land Rover
Lexus
Lincoln
Lotus
Maserati
Maybach
Mazda
Mercedes-Benz
Mercury
MINI
Mitsubishi
Nissan
Oldsmobile
Plymouth
Pontiac
Porsche
Rolls-Royce
Saab
Saturn
Scion
Subaru
Suzuki
Toyota
Volkswagen
Volvo';
  $categories = 'Passenger Car
Luxury Car
Sports Car
Sport Utility Vehicle
Van/Minivan
Pickup Truck';
  $conditions = 'New
Pre-Owned';
  $transmissions = 'Automatic
Manual
Tiptronic';
  $fuels = 'Biodiesel
CNG
Diesel
Electric
Ethanol-FFV
Gasoline
Hybrid-Electric
Steam
Other';
  $status = 'Available
Sale Pending
Sold';
  echo '
<div id=\'setup_content\'>
	
	';
  if (!($_POST[step]))
  {
    echo '	
		<p><strong>Welcome to the ';
    echo $product;
    echo ' ';
    echo $version;
    echo ' Setup Wizard.</strong></p>
		
		<p>This Setup Wizard will guide you through the installation of the ';
    echo $product;
    echo ' 
		application. If you have any questions during the setup process please refer 
		to the installation section of the ';
    echo $product;
    echo ' user\'s manual.</p>
		
		<p><form action=\'';
    echo $_SERVER[PHP_SELF];
    echo '\' method=\'post\'>
		<input type=\'submit\' value=\'Continue\'/>
		<input type=\'hidden\' name=\'step\' value=\'1\'/>
		</form></p>
	
	';
  }

  if ($_POST[step] == 1)
  {
    foreach ($dirset as $dir)
    {
      if (is_dir ($dir))
      {
        if (!(is_writable ($dir)))
        {
          $error .= 'You must change the <b>' . $dir . '</b> directory\'s permissions to 777.<br/>';
          continue;
        }

        continue;
      }
      else
      {
        $error .= 'The <b>' . $dir . '</b> directory was not found.<br/>';
        continue;
      }
    }

    if (is_file ($confdata))
    {
      if (!(is_writable ($confdata)))
      {
        $error .= 'You must change the <b>' . $confdata . '</b> file\'s permissions to 777.<br/>';
      }
      else
      {
        $data = implode ('', file ($confdata));
        if (substr (trim ($data), 0, 5) == '<?php')
        {
          $error .= '' . $product . ' has already been installed in this directory.  To reinstall ' . $product . '
					in this directory you must delete the contents of the <b>' . $confdata . '</b> file.<br/>';
        }
      }
    }
    else
    {
      $error .= 'The <b>' . $confdata . '</b> file was not found.<br/>';
    }

    if ($error)
    {
      echo '<p>Directory structure check and file check results:</p>';
      echo '<div id=\'msg-error\'>' . $error . '</div>';
      echo '<p>Please correct the errors above and click your browser\'s \'Refresh\' button.</p>';
    }
    else
    {
      $_POST[step] = 2;
    }
  }

  if ($_POST[step] == 2)
  {
    if ($_POST[submit])
    {
      if (!(trim ($_POST[dbhost])))
      {
        $error .= 'The <b>Database Host</b> field was left blank.<br/>';
      }

      if (!(trim ($_POST[dbname])))
      {
        $error .= 'The <b>Database Name</b> field was left blank.<br/>';
      }

      if (!($error))
      {
        ($link = @mysql_connect ($_POST[dbhost], $_POST[dbuser], $_POST[dbpass]) OR $error = 'Database connection failed: ' . mysql_error ());
        if (!($error))
        {
          $fp = fopen ($confdata, 'w');
          fwrite ($fp, '<?php
');
          fwrite ($fp, '$dbhost = \'' . $_POST[dbhost] . '\';
');
          fwrite ($fp, '$dbuser = \'' . $_POST[dbuser] . '\';
');
          fwrite ($fp, '$dbpass = \'' . $_POST[dbpass] . '\';
');
          fwrite ($fp, '$session_path = \'' . $_POST[session_path] . '\';

');
          fwrite ($fp, '// To turn ON demo mode change the value below to 1
');
          fwrite ($fp, '// To turn OFF demo mode change the value below to 0
');
          fwrite ($fp, '$demo = 0;

');
          fwrite ($fp, '// To disable the style overrides used by the Color Options
');
          fwrite ($fp, '// feature in the control panel set the value below to 1
');
          fwrite ($fp, '$override = 0;

');
          fwrite ($fp, '?>
');
          fclose ($fp);
          $result = mysql_query ('SHOW DATABASES LIKE \'' . $_POST['dbname'] . '\'', $link);
          if (mysql_num_rows ($result) == 0)
          {
            if (!(mysql_query ('CREATE DATABASE IF NOT EXISTS ' . $_POST['dbname'], $link)))
            {
              $error .= 'The database <b>' . $_POST['dbname'] . '</b> does not exist.<br/>
						<br/>
						Database creation attempt failed.<br/>
						<br/>' . mysql_error ($link) . '<br/>';
            }
          }

          if (!($error))
          {
            mysql_select_db ($_POST[dbname], $link);
            $fp = fopen ($confdata, 'a');
            fwrite ($fp, '<?php
');
            fwrite ($fp, '$dbname = \'' . $_POST[dbname] . '\';
');
            fwrite ($fp, '?>
');
            fclose ($fp);
            $_POST[dbacct] = $_POST[prefix] . 'users';
            $_POST['dblist'] = $_POST[prefix] . 'listings';
            $_POST[dbimgs] = $_POST[prefix] . 'images';
            $_POST[dbloca] = $_POST[prefix] . 'locations';
            $_POST[dbcapt] = $_POST[prefix] . 'captions';
            $_POST[dbfeat] = $_POST[prefix] . 'features';
            $_POST[dbstat] = $_POST[prefix] . 'stats';
            $_POST[dbconf] = $_POST[prefix] . 'config';
            $fp = fopen ($confdata, 'a');
            fwrite ($fp, '<?php
');
            fwrite ($fp, '$dbacct = \'' . $_POST[dbacct] . '\';
');
            fwrite ($fp, '$dblist = \'' . $_POST['dblist'] . '\';
');
            fwrite ($fp, '$dbimgs = \'' . $_POST[dbimgs] . '\';
');
            fwrite ($fp, '$dbloca = \'' . $_POST[dbloca] . '\';
');
            fwrite ($fp, '$dbcapt = \'' . $_POST[dbcapt] . '\';
');
            fwrite ($fp, '$dbfeat = \'' . $_POST[dbfeat] . '\';
');
            fwrite ($fp, '$dbstat = \'' . $_POST[dbstat] . '\';
');
            fwrite ($fp, '$dbconf = \'' . $_POST[dbconf] . '\';

');
            fwrite ($fp, '?>
');
            fclose ($fp);
            mysql_query ('DROP TABLE IF EXISTS ' . $_POST['dbacct'], $link);
            (mysql_query ('CREATE TABLE ' . $_POST['dbacct'] . ' (id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
					first_name VARCHAR(35),
					last_name VARCHAR(35),
					user_type TINYINT(1) NOT NULL,
					location INT UNSIGNED,
					last_login INT UNSIGNED,
					
					username VARCHAR(20),
					password CHAR(32),
					email VARCHAR(50),
					phone VARCHAR(20),
					contact TINYINT(1) UNSIGNED,
					
					maxlist VARCHAR(5),
					expire INT UNSIGNED,
					listings INT UNSIGNED NOT NULL,
					status TINYINT(1) NOT NULL,
					hide TINYINT(1) NOT NULL,
					pending TINYINT(1) NOT NULL
					)', $link) OR $error .= 'The <b>User Accounts</b> table could not be created.<br/>' . mysql_error ($link) . '<br/>');
            mysql_query ('DROP TABLE IF EXISTS ' . $_POST['dblist'], $link);
            (mysql_query ('CREATE TABLE ' . $_POST['dblist'] . ' (id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
					userid INT UNSIGNED NOT NULL,
					location INT UNSIGNED NOT NULL,
					user_type TINYINT(1) UNSIGNED,
					
					added INT UNSIGNED,
					updated INT UNSIGNED,
					expire INT UNSIGNED,
					user_expire INT UNSIGNED,
					viewed INT UNSIGNED,
					images INT UNSIGNED,
					
					hide TINYINT(1) UNSIGNED,
					featured TINYINT(1) UNSIGNED,
					status VARCHAR(50),
					pending TINYINT(1) NOT NULL,
					
					stock VARCHAR(20),
					vin VARCHAR(50),
					model_year INT UNSIGNED NULL,
					make VARCHAR(100),
					model VARCHAR(100),
					cond VARCHAR(100),
					category VARCHAR(100),
					category2 VARCHAR(100),
					mileage INT UNSIGNED NULL,
					mileage_alt VARCHAR(150),
					price INT UNSIGNED NULL,
					sale INT UNSIGNED NULL,
					price_alt VARCHAR(100),
					
					exterior VARCHAR(100),
					interior VARCHAR(100),
					doors VARCHAR(10),
					fuel VARCHAR(50),
					drive VARCHAR(100),
					
					engine VARCHAR(100),
					trans VARCHAR(100),
					top_speed VARCHAR(100),
					horsepower VARCHAR(100),
					torque VARCHAR(100),
					towing VARCHAR(100),
										
					features TEXT,
					description TEXT,
					
					tagline VARCHAR(200),
					link_url VARCHAR(200),
					link_text VARCHAR(25),
					ebay_url VARCHAR(200)
					)', $link) OR $error .= 'The <b>Listings</b> table could not be created.<br/>' . mysql_error ($link) . '<br/>');
            mysql_query ('DROP TABLE IF EXISTS ' . $_POST['dbimgs'], $link);
            (mysql_query ('CREATE TABLE ' . $_POST['dbimgs'] . ' (id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
					listid INT(10) UNSIGNED,
					fname CHAR(19)
					)', $link) OR $error .= 'The <b>Images</b> table could not be created.<br/>' . mysql_error ($link) . '<br/>');
            mysql_query ('DROP TABLE IF EXISTS ' . $_POST['dbloca'], $link);
            (mysql_query ('CREATE TABLE ' . $_POST['dbloca'] . ' (id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
					listings INT UNSIGNED NOT NULL,
					name VARCHAR(150),
					email VARCHAR(50),
					url VARCHAR(150),
					phone VARCHAR(20),
					fax VARCHAR(20),
					address VARCHAR(50),
					address2 VARCHAR(50),
					city VARCHAR(50),
					state VARCHAR(50),
					zip VARCHAR(20),
					country VARCHAR(50),
					hide TINYINT(1) UNSIGNED
					)', $link) OR $error .= 'The <b>Dealer Locations</b> table could not be created.<br/>' . mysql_error ($link) . '<br/>');
            mysql_query ('DROP TABLE IF EXISTS ' . $_POST['dbcapt'], $link);
            (mysql_query ('CREATE TABLE ' . $_POST['dbcapt'] . ' (id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, 
					caption VARCHAR(50)
					)', $link) OR $error .= 'The <b>Feature Captions</b> table could not be created.<br/>' . mysql_error ($link) . '<br/>');
            mysql_query ('DROP TABLE IF EXISTS ' . $_POST['dbfeat'], $link);
            (mysql_query ('CREATE TABLE ' . $_POST['dbfeat'] . ' (id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY
					)', $link) OR $error .= 'The <b>Features</b> table could not be created.<br/>' . mysql_error ($link) . '<br/>');
            mysql_query ('DROP TABLE IF EXISTS ' . $_POST['dbstat'], $link);
            (mysql_query ('CREATE TABLE ' . $_POST['dbstat'] . ' (id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
					listid INT UNSIGNED,
					userid INT UNSIGNED,
					user_type TINYINT(1) UNSIGNED,
					tstamp INT UNSIGNED,
					dstamp INT UNSIGNED,
					mstamp TINYINT(2) UNSIGNED,
					ystamp INT UNSIGNED,
					hstamp TINYINT(2) UNSIGNED,
					ip VARCHAR(15)
					)', $link) OR $error .= 'The <b>Listing Statistics</b> table could not be created.<br/>' . mysql_error ($link) . '<br/>');
            mysql_query ('DROP TABLE IF EXISTS ' . $_POST['dbconf'], $link);
            (mysql_query ('CREATE TABLE ' . $_POST['dbconf'] . ' (id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
					currency VARCHAR(10),
					units VARCHAR(15),
					state VARCHAR(20),
					zip VARCHAR(20),
					vin VARCHAR(20),
					dformat VARCHAR(20),
					tformat VARCHAR(20),
					toffset INT NOT NULL,
					
					perpage INT UNSIGNED,
					srt VARCHAR(20),
					show_dealer TINYINT(1) UNSIGNED,
					featured TINYINT(1) UNSIGNED,
					cp_perpage INT UNSIGNED,
					cp_srt VARCHAR(20),
					location INT UNSIGNED,
					cp_uperpage INT UNSIGNED,
					cp_usrt VARCHAR(20),
					cp_lperpage INT UNSIGNED,
					cp_lsrt VARCHAR(20),
					
					user_loc TINYINT(1) UNSIGNED,
					user_mod TINYINT(1) UNSIGNED,
					user_expire TINYINT(1) UNSIGNED,
					user_feature TINYINT(1) UNSIGNED,
					user_hide TINYINT(1) UNSIGNED,
					
					admin_loc TINYINT(1) UNSIGNED,
					admin_mod TINYINT(1) UNSIGNED,
					admin_expire TINYINT(1) UNSIGNED,
					admin_feature TINYINT(1) UNSIGNED,
					admin_hide TINYINT(1) UNSIGNED,
					
					makes TEXT,
					categories TEXT,
					conditions TEXT,
					status TEXT,
					transmissions TEXT,
					fuels TEXT,
					
					default_makes TEXT,
					default_categories TEXT,
					default_conditions TEXT,
					default_status TEXT,
					default_transmissions TEXT,
					default_fuels TEXT,
					
					color_dk VARCHAR(6),
					color_d2 VARCHAR(6),
					color_md VARCHAR(6),
					color_lg VARCHAR(6),
					color_t1 VARCHAR(6),
					color_t2 VARCHAR(6),
					color_tb VARCHAR(6),
					color_ts VARCHAR(6),
					color_tl VARCHAR(6),
					color_lk VARCHAR(6),
					color_bd VARCHAR(6),
					color_ok VARCHAR(6),
					color_al VARCHAR(6),
					color_er VARCHAR(6)
					)', $link) OR $error .= 'The <b>Configuration</b> table could not be created.<br/>' . mysql_error ($link) . '<br/>');
            if (!($error))
            {
              (mysql_query ('INSERT INTO ' . $_POST['dbconf'] . ' VALUES(\'0\',
						\'$\',
						\'Miles\',
						\'State\',
						\'Zip Code\',
						\'VIN\',
						\'m/d/Y\',
						\'g:i a\',
						\'0\',
						
						\'5\',
						\'make ASC\',
						\'1\',
						\'0\',
						\'10\',
						\'added DESC\',
						\'1\',
						\'20\',
						\'last_name ASC\',
						\'20\',
						\'name ASC\',
						
						\'0\',
						\'0\',
						\'0\',
						\'0\',
						\'1\',
						
						\'0\',
						\'0\',
						\'1\',
						\'1\',
						\'1\',
						
						\'' . $makes . '\',
						\'' . $categories . '\',
						\'' . $conditions . '\',
						\'' . $status . '\',
						\'' . $transmissions . '\',
						\'' . $fuels . '\',
						
						\'' . $makes . '\',
						\'' . $categories . '\',
						\'' . $conditions . '\',
						\'' . $status . '\',
						\'' . $transmissions . '\',
						\'' . $fuels . '\',
						
						\'369\',
						\'4F7EAD\',
						\'69C\',
						\'D7E9F5\',
						\'FFF\',
						\'FFC73C\',
						\'275380\',
						\'444\',
						\'FFF\',
						\'369\',
						\'E7F1F8\',
						\'393\',
						\'F60\',
						\'C00\'
						)', $link) OR $error .= 'The <b>Default Configuration</b> information could not be stored.<br/>' . mysql_error ($link) . '<br/>');
              if (!($error))
              {
                $_POST = array ();
                $_POST[step] = 3;
                $_POST[submit] = false;
              }
              else
              {
                $_POST[submit] = false;
              }
            }
            else
            {
              $_POST[submit] = false;
            }
          }
          else
          {
            $_POST[submit] = false;
          }
        }
        else
        {
          $_POST[submit] = false;
        }
      }
      else
      {
        $_POST[submit] = false;
      }
    }
  }

  if ($_POST[step] == 2)
  {
    if (!($_POST[submit]))
    {
      echo '	
		<p>Please enter your database information below.  This information is usually provided by your ISP.</p>
		
		<p>Required fields are indicated by an asterisk (*)</p>
		
		';
      if ($error)
      {
        echo '<div id=\'msg-error\'>' . $error . '</div>';
      }

      echo '		
		<form action=\'';
      echo $_SERVER[PHP_SELF];
      echo '\' method=\'post\'>
		<div class=\'form\'>
			
			<table width=\'100%\' border=\'0\' cellpadding=\'0\' cellspacing=\'0\'>
				<tr>
					<td class=\'label\'><label for=\'dbhost\'>Database Host: *</label></td>
					<td><input class=\'widtha\' type=\'text\' id=\'dbhost\' name=\'dbhost\' value=\'';
      echo $_POST[dbhost];
      echo '\'/></td>
				</tr>
				<tr>
					<td><label for=\'dbuser\'>Database Username:</label></td>
					<td><input class=\'widtha\' type=\'text\' id=\'dbuser\' name=\'dbuser\' value=\'';
      echo $_POST[dbuser];
      echo '\'/></td>
				</tr>
				<tr>
					<td><label for=\'dbpass\'>Database Password:</label></td>
					<td><input class=\'widtha\' type=\'password\' id=\'dbpass\' name=\'dbpass\' value=\'';
      echo $_POST[dbpass];
      echo '\'/></td>
				</tr>
				<tr>
					<td><label for=\'dbname\'>Database Name: *</label></td>
					<td><input class=\'widtha\' type=\'text\' id=\'dbname\' name=\'dbname\' value=\'';
      echo $_POST[dbname];
      echo '\'/></td>
				</tr>
				
				<tr>
					<td><label for=\'prefix\'>Table Name Prefix:</label></td>
					<td><input class=\'widthb\' type=\'text\' id=\'prefix\' name=\'prefix\' value=\'';
      echo $_POST[prefix];
      echo '\'/>
					( Example: abc_ )</td>
				</tr>
				
				<tr><td colspan=\'2\'><p>If your server requires you to specify the session path please enter it below.</p></td></tr>
				
				<tr>
					<td><label for=\'session_path\'>Session Path:</label></td>
					<td><input class=\'widtha\' type=\'text\' id=\'session_path\' name=\'session_path\' value=\'';
      echo $_POST[session_path];
      echo '\'/></td>
				</tr>
				
			</table>
			
		</div>
		<input type=\'submit\' name=\'submit\' value=\'Continue\'/>
		<input type=\'hidden\' name=\'step\' value=\'2\'/>
		</form>
	
	';
    }
  }

  if ($_POST[step] == 3)
  {
    if ($_POST[submit])
    {
      $_POST = safe_data ($_POST, 'query');
      if (!($_POST[first_name]))
      {
        $error .= 'Please enter a <b>First Name</b> for this user.<br/>';
      }

      if (!($_POST[last_name]))
      {
        $error .= 'Please enter a <b>Last Name</b> for this user.<br/>';
      }

      if (!(check_user ($_POST[username])))
      {
        $error .= 'The <b>Username</b> your have chosen is invalid.<br/>';
      }

      if (!(check_pass ($_POST[password], $_POST[confirm])))
      {
        $error .= 'The <b>password</b> you have chosen does not meet the requirements specified.<br/>';
      }

      if (!($_POST[email]))
      {
        $error .= 'Please enter an <b>email address</b> for this user.<br/>';
      }

      if (!($error))
      {
        $link = mysql_connect ($dbhost, $dbuser, $dbpass);
        mysql_select_db ($dbname, $link);
        mysql_query ('DELETE FROM ' . $dbacct, $link);
        (mysql_query ('INSERT INTO ' . $dbacct . ' VALUES(\'0\',
			\'' . $_POST['first_name'] . '\',
			\'' . $_POST['last_name'] . '\',
			\'3\',
			\'\',
			\'\',
			
			\'' . $_POST['username'] . '\',
			\'' . md5 ($_POST[password]) . ('\',
			\'' . $_POST['email'] . '\',
			\'' . $_POST['phone'] . '\',
			\'' . $_POST['contact'] . '\',
			
			\'\',
			\'\',
			\'0\',
			\'\',
			\'\',
			\'0\')'), $link) OR $error .= 'The <b>Superuser Account</b> could not be created.<br/>' . mysql_error ($link) . '<br/>');
        if (!($error))
        {
          $_POST = array ();
          $_POST[step] = 4;
          $_POST[submit] = false;
        }
        else
        {
          $_POST[submit] = false;
        }
      }
      else
      {
        $_POST[submit] = false;
      }
    }
  }

  if ($_POST[step] == 3)
  {
    if (!($_POST[submit]))
    {
      echo '	
		<p>Please use the form below to create your Superuser account.</p>
		
		<p>Required fields are indicated by an asterisk (*)</p>
		
		';
      if ($error)
      {
        echo '<div id=\'msg-error\'>' . $error . '</div>';
      }

      echo '		
		<form action=\'';
      echo $_SERVER[PHP_SELF];
      echo '\' method=\'post\'>
		<div class=\'form\'>
			
			<table width=\'100%\' border=\'0\' cellpadding=\'0\' cellspacing=\'0\'>
				<tr>
					<td class=\'label\'><label for=\'first_name\'>First Name: *</label></td>
					<td><input class=\'widtha\' type=\'text\' id=\'first_name\' name=\'first_name\' value=\'';
      echo $_POST[first_name];
      echo '\'/></td>
				</tr>
				<tr>
					<td><label for=\'last_name\'>Last Name: *</label></td>
					<td><input class=\'widtha\' type=\'text\' id=\'last_name\' name=\'last_name\' value=\'';
      echo $_POST[last_name];
      echo '\'/></td>
				</tr>
				<tr>
					<td><label for=\'username\'>Username: *</label></td>
					<td>
						<input class=\'widthb\' type=\'text\' id=\'username\' name=\'username\' value=\'';
      echo $_POST[username];
      echo '\'/>
						(6 to 20 characters, no punctuation)
					</td>
				</tr>
				<tr>
					<td><label for=\'password\'>Password: *</label></td>
					<td>
						<input class=\'widthb\' type=\'password\' id=\'password\' name=\'password\' value=\'';
      echo $_POST[password];
      echo '\'/>
						(6 to 20 characters, no punctuation)
					</td>
				</tr>
				<tr>
					<td><label for=\'confirm\'>Confirm Password: *</label></td>
					<td><input class=\'widthb\' type=\'password\' id=\'confirm\' name=\'confirm\' value=\'';
      echo $_POST[confirm];
      echo '\'/></td>
				</tr>
				
				<tr>
					<td><label for=\'email\'>Email Address: *</label></td>
					<td><input class=\'widtha\' type=\'text\' id=\'email\' name=\'email\' value=\'';
      echo $_POST[email];
      echo '\'/></td>
				</tr>
				
				<tr>
					<td><label for=\'phone\'>Phone Number:</label></td>
					<td><input class=\'widtha\' type=\'text\' id=\'phone\' name=\'phone\' value=\'';
      echo $_POST[phone];
      echo '\'/></td>
				</tr>
				
				<tr>
					<td><label for=\'contact\'>Contact:</label></td>
					<td>
						<input class=\'check\' type=\'checkbox\' name=\'contact\' id=\'contact\' value=\'1\' ';
      if ($_POST[contact])
      {
        echo 'checked=\'checked\'';
      }

      echo '/>
						Check to display this contact information with this user\'s listings
					</td>
				</tr>
				
			</table>
			
		</div>
		<input type=\'submit\' name=\'submit\' value=\'Continue\'/>
		<input type=\'hidden\' name=\'step\' value=\'3\'/>
		</form>
	
	';
    }
  }

  if ($_POST[step] == 4)
  {
    if ($_POST[submit])
    {
      $_POST = safe_data ($_POST, 'query');
      if (!($_POST[name]))
      {
        $error .= 'Please enter a <b>Name</b> for this dealership.<br/>';
      }

      if (!($_POST[email]))
      {
        $error .= 'Please enter an <b>email address</b> for this dealership.<br/>';
      }

      if (!($_POST[phone]))
      {
        $error .= 'Please enter an <b>phone number</b> for this dealership.<br/>';
      }

      if (!($error))
      {
        $link = mysql_connect ($dbhost, $dbuser, $dbpass);
        mysql_select_db ($dbname, $link);
        mysql_query ('DELETE FROM ' . $dbloca, $link);
        (mysql_query ('INSERT INTO ' . $dbloca . ' VALUES(\'0\',
			\'0\',
			\'' . $_POST['name'] . '\',
			\'' . $_POST['email'] . '\',
			\'\',
			\'' . $_POST['phone'] . '\',
			\'\',
			\'\',
			\'\',
			\'\',
			\'\',
			\'\',
			\'\',
			\'\')', $link) OR $error .= 'The <b>default dealership</b> could not be created.<br/>' . mysql_error ($link) . '<br/>');
        if (!($error))
        {
          $_POST = array ();
          $_POST[step] = 5;
          $_POST[submit] = false;
        }
        else
        {
          $_POST[submit] = false;
        }
      }
      else
      {
        $_POST[submit] = false;
      }
    }
  }

  if ($_POST[step] == 4)
  {
    if (!($_POST[submit]))
    {
      echo '	
		<p>Use the form below to create your default dealership location.</p>
		
		<p>Required fields are indicated by an asterisk (*)</p>
		
		';
      if ($error)
      {
        echo '<div id=\'msg-error\'>' . $error . '</div><br/>';
      }

      echo '		
		<form action=\'';
      echo $_SERVER[PHP_SELF];
      echo '\' method=\'post\'>
		<div class=\'form\'>
			
			<table width=\'100%\' border=\'0\' cellpadding=\'0\' cellspacing=\'0\'>
				
				<tr>
					<td class=\'label\'><label for=\'name\'>Dealership Name: *</label></td>
					<td><input class=\'widtha\' type=\'text\' id=\'name\' name=\'name\' value=\'';
      echo $_POST[name];
      echo '\'/></td>
				</tr>
				<tr>
					<td><label for=\'email\'>Email Address: *</label></td>
					<td><input class=\'widtha\' type=\'text\' id=\'email\' name=\'email\' value=\'';
      echo $_POST[email];
      echo '\'/></td>
				</tr>
				<tr>
					<td><label for=\'phone\'>Phone Number: *</label></td>
					<td><input class=\'widtha\' type=\'text\' id=\'phone\' name=\'phone\' value=\'';
      echo $_POST[phone];
      echo '\'/></td>
				</tr>
				
			</table>
			
		</div>
		<input type=\'submit\' name=\'submit\' value=\'Continue\'/>
		<input type=\'hidden\' name=\'step\' value=\'4\'/>
		</form>
	
	';
    }
  }

  if ($_POST[step] == 5)
  {
    echo '<p style=\'text-align:center;\'><strong>';
    echo '<br><br>WWW.NULLED.WS<br><br>';
    echo '<a href="./admin/">ADMIN LOGIN</a>';
    echo '<br><br></strong></p>';

  }

  echo '	
</div>
	
';
  require 'admin/cp-includes/inc-wfooter.php';
?>