<?php 

require __DIR__ . '/core/Globals.php';
$d = new \DateTime('now');
if(!file_exists("dumps")) mkdir("dumps");
exec(_DB_DUMPER . ' --user=' . _DB_USER . ' --password=' . _DB_PASSWORD .' --host=' . _DB_HOST . ' ' . _DB_NAME . ' > dumps\\' . $d->format('Y-m-d.G.i') . '.sql');
echo "Completed";

