$(document).ready(function() {
	
	_server = {
		getTable: function() {
			$.post('/get-table', function(data){
				//if (!data)
			}, 'json');
		},
		
		sendTeams: function(data) {			
			return $.ajax({url: '/send-teams', 'async': false, 'data': data, 'type': 'POST', 'dataType': 'json'});			
		}
		
		
	}
	
	_vars = {
		 min_teams_count: 4,
		 container: '#container'
	},
	
	_form = {
			gen_input: function(params) {
					return $("<div></div>", {'class': 'form-group'}).append($("<input>", params));
			}
	},
	
	_table = {
		gen_table: function(where ,data) {
			
		},
		
		form: null,
		
		gen_add_teams_form: function(where) {
			
			
			this.form = $("<form/>", 
                 { type:'POST' }
            );
			this.form.append($('<h1>ADD TEAMS TO TURNAMENT</h1>'));
			
			for (let i = 0; i < _vars.min_teams_count; i++) {
				
				this.form.append(_form.gen_input({'type': "text", "required": "required", 'name': 'teams[]', 'placeholder': 'Input team name', class: "form-control"}));
			}
			
			
			this.form.append(_form.gen_input({"type": "button", "value": "Add more team", "class": "btn", "id": "add_team"}));
			this.form.append(_form.gen_input({"type": "submit", "value": "Send data", "class": "btn btn-primary", "id": "send_form"}));
			
			
			
			where.append(this.form);
			
			$(this.form).on('submit', function(){
				
					let data = _server.sendTeams($( this ).serialize());
					
					if (data.res) {
							$(this).hide();
							
					}
					
					return false;
					
			});
			
			$(this.form).on('click', '#add_team', function(){
				
					$(this).before(_form.gen_input({'type': "text", "required": "required", 'name': 'teams[]', 'placeholder': 'Input team name', class: "form-control"}));
					
					return false;
					
			});
		},
		
		
	}
	
	_table.gen_add_teams_form($(_vars.container));
	
	
});

