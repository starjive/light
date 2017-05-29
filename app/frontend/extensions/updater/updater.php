#!/usr/bin/env php
<?php
/**
 * A quick and dirty script to update the WooThemes framework. 
 * 
 * This script assumes that any folder with the file functions-changelog.txt is a WooThemes folder
 * (I know.. this is dangerous, but in our quick tests, it seems to be accurate).
 * 
 * Luckily we have nightly backups, so if we mess this up, we'll be able to restore from the night before.
 * Hope you do too. 
 * 
 * This script has no warranty, if it kills your dog, cat, server, site.. I'm sorry. 
 * 
 * Author: Vid Luther (vid@zippykid.com)
 * http://zippykid.com/
 * 
 */


/**
 * This cleans up the tmp folder and downloads the latest/greatest woothemes framework
 * 
 * @return boolean 
 */
function start_clean()
{
	if(is_file("/tmp/woofunctions.tar.gz")) {
		unlink("/tmp/woofunctions.tar.gz"); 
	}
	if(is_dir("/tmp/woofunctions")) {
		shell_exec("rm -rf /tmp/woofunctions");  		
	}
	
	shell_exec("wget -O /tmp/woofunctions.tar.gz http://c329558.r58.cf1.rackcdn.com/woofunctions.tar.gz"); 
	shell_exec("cd /tmp && tar zxf woofunctions.tar.gz"); 
	
	return true; 
}



start_clean(); 


$possible_sf_installs = shell_exec('find . -name functions-changelog.txt -print'); 

// this is retarded I know.. but it's a hack anyway :)
$array_of_sf = explode("\n", $possible_sf_installs); 


foreach($array_of_sf AS $theme_location) {
	$dirname = dirname($theme_location);
	$update = update_sf(trim($dirname));

	if($update === true) { 
		echo " update successful \n"; 
	} else {
		echo "Update failed :( \n"; 
	} 
}


/**
 * Copies the files from the downloaded woothemes framework
 * into what we think is a woothemes framework install.
 * We don't really know if this will update the wrong folder
 * or not yet.
 * 
 * 
 * @param  string $path
 * @return boolean  
 */
function update_sf($path) {
	// sometimes we get an extra item in the array that's just blank. we don't need to do anythign here. 
	if(strlen($path) === 0) {
		echo "Not really a folder.. ";
		return true;  
	} 	
	echo "Updating $path... ";
	$copy = shell_exec("cp -r /tmp/woofunctions/*  $path"); 
	
	if(strlen($copy) ===  0) { 
		return true; 
	} else {
		return false; 
	} 	
}


/**
 * This is an attempt to determine if a given path is a Woo themes folder or not.
 * In initial testing this method of checking the file names isn't conclusive. But
 * it's here in case someone smarter than me wants a crack at it. 
 * 
 * @param  string  $path the path we want to check 
 * @return boolean       is it woo or not?
 */
function is_it_sf($path)
{
	$pristine_files = scandir("/tmp/woofunctions"); 
	$installed_files = scandir("$path"); 

	$different_files = array_diff($pristine_files, $installed_files); 

	// this is the hard part.. if there is no difference, this is framework folder.. 
	// if there is a difference, then the installed files may just be an older folder..
}


?>