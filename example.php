<?php
	/***********************************************************************************************************

		The following example shows how to use the WMI class to query system information using the Windows
		Management Interface.

	 ***********************************************************************************************************/
	require ( 'Wmi.phpclass' ) ;

	if  ( php_sapi_name ( )  !=  'cli' ) 
		echo ( "<pre>" ) ;

	// Create an object for accessing the Windows Management Interface
	$wmi		=  new Wmi ( ) ;

	// Display the list of processes currently running on your system (pid + command line).
	// (for more information about the Win32_Process WMI class, see : https://msdn.microsoft.com/en-us/library/aa394372(v=vs.85).aspx)
	echo ( "Process list :\n" ) ;
	$process_list	=  $wmi -> QueryInstances ( 'Win32_Process' ) ;

	foreach  ( $process_list  as  $process )
		echo ( "\tProcess : ({$process -> ProcessId}) {$process [ 'CommandLine' ]}\n" ) ;

	// Display the list of printers configured on your system
	// (for more information about the Win32_Printer WMI class, see : https://msdn.microsoft.com/en-us/library/aa394363(v=vs.85).aspx)
	echo ( "\n\nPrinter list :\n" ) ;
	$printer_list	=  $wmi -> QueryInstances ( 'Win32_Printer' ) ;

	foreach ( $printer_list  as  $printer )
		echo ( "\t{$printer -> Caption}\n" ) ;

	// Display all the logical drives defined on your system
	// (for more information about the Win32_LogicalDrive WMI class, see : https://msdn.microsoft.com/en-us/library/aa394173(v=vs.85).aspx)
	echo ( "\n\nLogical drives :\n" ) ;
	$logical_drives	=  $wmi -> QueryInstances ( 'Win32_LogicalDisk' ) ;

	foreach  ( $logical_drives  as  $drive )
		echo ( "\t{$drive -> Name} ({$drive -> VolumeName})\n" ) ;

	// Display removable logical drives list, using the Query() method with a WHERE clause instead of calling QueryInstances()
	echo ( "\n\nRemovable logical drives :\n" ) ;
	$logical_drives	=  $wmi -> Query ( 'SELECT * FROM Win32_LogicalDisk WHERE MediaType = 11' ) ;

	foreach  ( $logical_drives  as  $drive )
		echo ( "\t{$drive -> Name}\n" ) ;

