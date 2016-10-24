# INTRODUCTION #

The *Windows Management Instrumentation* interface (WMI) is a Windows API that gives you access to internal Windows information such as the processes currently running, the printers currently configured, the drives currently defined on you system, and much more...

WMI information is composed of *namespaces* ; each namespace contains its own set of classes. For example, there is a namespace called **\ROOT\CIMV2**, where you will be able to find useful information such as :

- Processes currently running (*Win32\_Process* class)
- Physical disks (*Win32\_DiskDrive*)
- Logical disks (*Win32\_LogicalDrive*)
- Printers (*Win32\_Printer*)
- etc.

Each class has its own set of instances ; to understand what an instance is, take the example of the *Win32\_Process* class : each instance of this class is actually describing one of the processes currently running on your system. Being an object, it has its own set of properties, such as *ProcessId* or *CommandLine*.

The WMI classes belong to a class hierarchy ; describing this hierarchy would go beyond the scope of this help file and you can find excellent documentation about that by googling around the net. All you have to know so far is that most classes have properties in common. Of course, they also have properties that are unique to the class itself !

WMI can be queried using a very limited subset of a SQL-like language ; you can only perform SELECT queries on classes (tables) and use WHERE clauses to filter out the results.

Windows is bundled with a utility called *wbemtest*, which allows you to more or less directly run such queries. However, the interface is really tedious. This is why I would like to suggest the excellent **WmiExplorer** tool ([http://wmie.codeplex.com/](http://wmie.codeplex.com/ "http://wmie.codeplex.com/")), which provides a really easy-to-use user interface and will allow you to quickly explore the WMI classes that are exposed by your system.

# OVERVIEW #

Using the **Wmi** PHP class is easy :

	require ( 'Wmi.phpclass' ) ;

	$wmi 	=  new Wmi ( ) ;

The **Wmi** class can be instantiated with any namespace you would like to query ; the default value is *ROOT\CIMV2*, which contains most of the really interesting classes for the most common usages.

Now, with your *$wmi* instance, you are ready to perform as many queries as you like ; for example, to retrieve the list of processes currently running on your system, use the *QueryInstances()* method, providing the class of objects you want to query :

	$process_list 	=  $wmi -> QueryInstances ( 'Win32_Process' ) ;

You can also perform queries using a WHERE clause. To do that, use the *Query()* method :

	$notepad_processes 	=  $wmi -> Query ( "SELECT * FROM Win32_Process WHERE Caption LIKE 'notepad%'" ) ;

In fact, the following :

	$wmi -> QueryInstances ( 'Win32_Process' )

is equivalent to :

	$wmi -> Query ( 'SELECT * FROM Win32_Process' )


Both methods return an array of objects inheriting from the **WmiInstance** class ; however, specific classes will be created on-the-fly to provide the caller with objects containing the properties belonging to the class that has been queried.

For example, querying the *WIN32\_Process* class will return an array of **Win32\_Process** objects, inheriting from the **WmiInstance** class ; as such, you will be able to access individual properties as you could do with any other object ; the properties will be specific to the **Win32\_Process** class. 

The following prints the process id and command line of the processes returned by the above query :

	foreach ( $process_list  as  $process )
		echo ( "Process : {$process -> ProcessId}, Command line : {$process [ 'CommandLine' ]}\n" ) ;

Note that you can use both the object notation (*$process -> ProcessId*) or the array notation (*$process [ 'CommandLine' ]*).

# REFERENCE #

## Wmi class ##

The **Wmi** class is a COM object wrapper that allows you to query against the WMI interface.

### Methods ####

#### Constructor ####

	public function  __construct ( $wmi_object_or_namespace = null ) ;

The class constructor creates a COM wrapper object that allows you to later query the WMI. If you do not specify any parameter, you will be connected to the Windows Management Instrumentation interface of your computer, using the following COM object :

	winmgmts:{impersonationLevel=Impersonate}!//./root/CIMV2

The **root/CIMV2** namespace give you access to the most commonly used WMI classes. Other namespaces are available, see the Microsoft documentation for that (or use the *WMI Explorer* tool !)

You can also connect to a remote computer ; see the *RemoteInstance()* method.

#### LocalInstance ####

	public static function  LocalInstance ( $namespace = 'winmgmts:{impersonationLevel=Impersonate}!//./root/CIMV2' )

Creates a WMI instance connected to your local computer ; the following code :

	$wmi 	=  Wmi::LocalInstance ( ) ;

is equivalent to :

	$wmi 	=  new Wmi ( ) ; 

#### RemoteInstance ####

	public static function  RemoteInstance ( $computer, $user, $password, $namespace = 'root\CIMV2', $locale = null, $domain = null )

Creates a WMI instance on a remote computer.

#### QueryInstances ####

	public function  QueryInstances ( $table, $base_class = 'WmiInstance', $namespace = false )

Queries the instances of the specified *table* (ie, an existing WMI class) and returns an array of objects.

The returned objects will be of a class whose name is given by the *table* parameter ; this class inherits from *WmiInstance* and is created on-the-fly, if needed.

The call to *QueryInstances* is just a shortcut :

	$wmi -> QueryInstances ( 'Win32_Process' ) ;

is equivalent to :

	$wmi -> Query ( 'SELECT * FROM Win32_Process' ) ;

#### Query #####

	public function  Query ( $query, $base_class = 'WmiInstance', $namespace = false )

The *Query* method can be used when you do not want to retrieve all the instances of a WMI class (called *$table* here) ; for example, if you want to retrieve all the processes running NOTEPAD.EXE, you do not have to use the *QueryInstances()* methods then perform a loop in PHP to only select the instances that are of interest to you ; you can simply call *Query()* using a WHERE clause :

	$notepad_process 	=  $wmi -> Query ( "SELECT * FROM Win32_Process WHERE Caption LIKE 'notepad%'" ) ;

Querying the whole process list can take several seconds ; if you're only interested in a particular kind of processes, then you can use the *Query* method to filter out the results, which will run pretty much faster.

## WmiInstance class ##

The **WmiInstance** class is not meant to be directly instantiated. It is used internally by the **Wmi** class to create classes that maps Windows Management Instrumentation instances to a PHP class of the same name.

It provides a safe environment to map the properties of each WMI class instance (which are simply COM objects) to standard PHP properties.

For example, such a query will return objects of class **Win32\_Process** class inheriting from **WmiInstance**.

	$list 		=  $wmi -> QueryInstances ( 'Win32_Process' ) ;

(the same will be true if you use the *Query* method instead of *QueryInstances*).

Within the **Win32\_Process** PHP object, you will find exactly the same properties as in the corresponding Windows Management Instrumentation classes.

So why wrapping WMI instances to PHP instances ? because a WMI instance is a COM object. If you want to access a property that does not exist on a COM object, a COM exception will be thrown. Unfortunately, COM exceptions are not catchable and will cause your program to fail without giving you any chance of recovery.

Of course, the **WmiInstance** class can throw exceptions by itself ; but they are all catchable...
   

 