var admin_search = new Vue({
	el: '#admin-search',

	data: {
		query: '',
		searchResults: null,
		waiting: false,
		lastQuery: '',
		lastQueryTime: 0,

		postTypes: null,
	},

	mounted: function() {
		axios.get(ajaxurl + '?action=wcst_admin_search_post_types')
			.then(function (response) {
				this.postTypes = Object.values(response.data)
				console.log(Object.values(response.data))
			}.bind(this))
			.catch(function (error) {
				console.log(error)
			});

		this.lastQueryTime = Date.now()
	},

	methods: {
		sendQuery() {
			this.lastQuery = this.query

			if ( this.query.length >= 3 ) {
				this.waiting = true
				this.searchResults = null

				if ( this.lastQueryTime + 5000 < Date.now() ) {

					this.lastQueryTime = Date.now()

					if ( this.query.length === this.lastQuery.length ) {
						var request = {
							query: this.query,
							post_types: this.postTypes
						}

						axios.post(ajaxurl + '?action=wcst_admin_search', request)
							.then(function (response) {
								this.searchResults = Array.from(response.data)
								this.waiting = false
								console.log(response)
							}.bind(this))
							.catch(function (error) {
								this.waiting = false
								console.log(error)
							}.bind(this));
					}
					else {
						console.log(this.query);
					}
				}
			}
			else {
				this.waiting = false
				this.searchResults = null
			}
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