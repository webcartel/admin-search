var admin_search = new Vue({
	el: '#admin-search',

	data: {
		query: '',
		searchResults: null,
	},

	mounted: function() {

	},

	methods: {
		sendQuery() {
			var form_data = new FormData
			form_data.append('query', this.query)
			axios.post(ajaxurl + '?action=wcst_admin_search', form_data)
				.then(function (response) {
					console.log(response);
					this.searchResults = Array.from(response.data)
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