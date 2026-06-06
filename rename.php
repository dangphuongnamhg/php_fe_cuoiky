<?php
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('resources/views'));
foreach ($it as $f) {
    if ($f->isFile() && $f->getExtension() === 'php') {
        $c = file_get_contents($f->getPathname());
        $nc = str_replace(
            ['FieldBook Admin', 'FieldBook —', 'FieldBook'], 
            ['SanGo Admin', 'SanGo —', 'SanGo'], 
            $c
        );
        if ($c !== $nc) {
            file_put_contents($f->getPathname(), $nc);
        }
    }
}
echo "Done replacing Admin titles.";
