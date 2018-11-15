var bmw_parser_Admin = new Vue({
	el: '#admin-search-admin',

	data: {
		url: '',
		pagesData: null,
		ready: false,
		singleurl: '',
	},

	mounted: function() {

	},

	methods: {
		sendUrl() {
			var form_data = new FormData
			form_data.append('url', this.url)
			axios.post(ajaxurl + '?action=pre_parse_run', form_data)
				.then(function (response) {
					console.log(response);
					this.pagesData = Array.from(response.data)
				}.bind(this))
				.catch(function (error) {
					console.log(error);
				});
		},
		

		shortString(str) {
			return (str.length >= 55) ? str.substr(0, 55) + '...' : str
		},
		

		timestampToDate(sec) {
		    var t = new Date(1970, 0, 1)
		    t.setSeconds(sec)
		    let year = t.getFullYear()
		    let month = (t.getMonth()+1) < 10 ? '0'+(t.getMonth()+1) : (t.getMonth()+1)
		    let day = t.getDate() < 10 ? '0'+t.getDate() : t.getDate()
		    let hours = t.getHours()
		    let minutes = t.getMinutes()
		    let seconds = t.getSeconds()
		    let date = hours +':'+ minutes// +' '+ day +'.'+ month +'.'+ year
		    return date
		},
	},
})