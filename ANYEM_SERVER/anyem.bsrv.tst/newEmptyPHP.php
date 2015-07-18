<?php
$judy = new Judy(Judy::STRING_TO_MIXED);
$judy['Hello'] = 'dump';
print $judy['Hello'];