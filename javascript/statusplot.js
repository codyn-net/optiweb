var StatusPlot = Class.create();

StatusPlot.prototype = {
	data: {},
	canvas: null,
	inner_padding: 3,
	outer_padding: 7,
	text_height: 15,
	name_font: '12px sans-serif',
	cap_height: 3,
	user_margin: 5,

	initialize: function (id) {
		this.id = id;
	},

	refresh: function(selection) {
		var myself = this;

		new Ajax.Request('statusplot.php', {
			method: 'get',
			parameters: selection,
			onSuccess: function (response) {
				if (response.responseJSON)
				{
					myself.update(response.responseJSON);
					myself.redraw();
				}
			}
		})
	},

	measure_user_width: function() {
		var context = this.canvas.getContext('2d');

		context.font = this.name_font;
		this.user_width = 0;

		for (var user in this.data.users)
		{
			// Measure user width
			var metrics = context.measureText(user);

			if (metrics.width > this.user_width)
			{
				this.user_width = metrics.width;
			}
		}
	},

	update: function(data) {
		this.canvas = $(this.id);
		this.data = data;

		this.measure_user_width();

		this.numbars = this.data.data.length;
		this.numrange = this.data.labels.length;

		this.pergroup = Math.ceil(this.numbars / this.numrange);

		var padinner = this.inner_padding * (this.pergroup - 1) * this.numrange;
		var padouter = this.outer_padding * (this.numrange - 1);

		this.barwidth = (this.canvas.width - 2 * this.user_margin - this.user_width - padinner - padouter) / this.numbars;
	},

	draw_bar: function(context, xpos, data) {
		var ypos = this.canvas.height - this.text_height;

		var colors = $H(this.data.users).values();

		for (var i = 0; i < data.length; ++i)
		{
			if (data[i] == 0)
			{
				continue;
			}

			var color = colors[i];
			context.fillStyle = color;

			var height = Math.round((data[i] / this.data.ymax) * (this.canvas.height - this.text_height - this.cap_height));

			ypos -= height;

			context.fillRect(xpos, ypos, this.barwidth, height);
		}

		if (this.cap_height > 0)
		{
			context.beginPath();
			context.moveTo(xpos, ypos);
			context.arc(xpos + this.cap_height, ypos, this.cap_height, Math.PI, 1.5 * Math.PI, false);
			context.arc(xpos + this.barwidth - this.cap_height, ypos, this.cap_height, 1.5 * Math.PI, 0, false);
			context.closePath();
			context.fill();
		}
	},

	draw_labels: function(context) {
		var w = this.inner_padding * (this.pergroup - 1) + this.pergroup * this.barwidth;
		var ypos = Math.round(this.canvas.height - this.text_height / 2) + 5;

		context.fillStyle = '#fff';

		for (var i = 0; i < this.data.labels.length; ++i)
		{
			var offset = i * (w + this.outer_padding);
			var xpos = Math.round(offset + w / 2);

			context.fillText(this.data.labels[i], xpos, ypos, w - 4);
		}
	},

	draw_users: function(context) {
		var height = 15;

		var posx = this.canvas.width - this.user_width;
		var posy = height;

		context.font = this.name_font;

		for (var user in this.data.users)
		{
			context.fillStyle = this.data.users[user];
			context.fillText(user, posx, posy);

			posy += height;
		}
	},

	redraw: function () {
		var context = this.canvas.getContext('2d');

		context.clearRect(0, 0, this.canvas.width, this.canvas.height);
		context.font = '10px sans-serif';

		var xpos = 0;

		for (var i = 0; i < this.numbars; i++)
		{
			this.draw_bar(context, xpos, this.data.data[i]);

			if ((i + 1) % this.pergroup == 0 && (i != 1 || this.pergroup <= 2))
			{
				xpos += this.barwidth + this.outer_padding;
			}
			else
			{
				xpos += this.barwidth + this.inner_padding;
			}
		}

		context.textAlign = 'center';
		this.draw_labels(context);

		context.textAlign = 'left';
		this.draw_users(context);
	}
};
