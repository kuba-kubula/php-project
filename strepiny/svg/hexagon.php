<?php

function smallhexagon($color,$number,$x,$y)
{

	echo '<g transform="translate('.$x.','.$y.')">';
	echo '<polygon points="-8,-13 8,-13 15,0 8,13 -8,13 -15,0" stroke-width="2" stroke-color="black" fill="'.$color.'" fill-opacity="0.8"></polygon>';
	echo '<text x="-9" y="1" style="font-family: Omikron, omikron-webfont; font-size: 20px; fill:black;">'.'0'.$number.'</text>';
	echo '</g>';
}


function largehexagon($color,$inner,$x,$y)
{

	echo '<g transform="translate('.$x.','.$y.')">';
	echo '<polygon points="-0,-35 30,-17 30,17 0,35 -30,17 -30,-17" stroke-width="3" stroke-color="black" fill="'.$color.'" fill-opacity="0.8"></polygon>';
	echo $inner;

	echo '</g>';
}
