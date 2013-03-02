<?php

function sector2uhel($sector){
	$sectornumber=(ord($sector[0])-65)*26000*26;
	$sectornumber+=$sector[1]*26000;
	$sectornumber+=$sector[2]*2600;
	$sectornumber+=$sector[3]*260;
	$sectornumber+=$sector[4]*26;
	$sectornumber+=(ord($sector[5])-65);

	$uhel=($sectornumber/17159999)*pi()*2;
	return $uhel;
}

function sectordistance($sector)
{
	$sectornumber=(ord($sector[0])-65)*26000*26;
	$sectornumber+=$sector[1]*26000;
	$sectornumber+=$sector[2]*2600;
	$sectornumber+=$sector[3]*260;
	$sectornumber+=$sector[4]*26;
  $sectornumber+=(ord($sector[5])-65);


  $distance=abs((17159999/2)-$sectornumber);
  $distancekoef=(($distance/17159999)*2);
  return $distancekoef;
}

function uhel2x($uhel)
{
	return 205*sin($uhel);
}

function uhel2y($uhel)
{
       return -205*cos($uhel);
}
