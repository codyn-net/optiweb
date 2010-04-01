<?php
	class Colors
	{
		static $colors = array(
			'#d0d0d0',
			'#a2a2a2',
			'#777777',
			'#ff8700',
			'#ffa25d',
			'#ffd0a7'
		);

		static $coloridx = 0;

		static function reset()
		{
			$coloridx = 0;
		}

		static function indexed($idx)
		{
			return self::$colors[intval($idx) % count(self::$colors)];
		}

		static function next()
		{
			$color = self::$colors[self::$coloridx];

			self::$coloridx++;

			if (self::$coloridx == count(self::$colors))
			{
				self::$coloridx = 0;
			}

			return $color;
		}
	}
?>
