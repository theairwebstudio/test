<?php

	class Match_list {
		
			private static $teams_count;		
			private static $matches = Array();
		
			
			
			public static function getTours($teams_count) {
				
				self::genMatchesTable($teams_count);
				
				do {
					$used_teams = Array();
					
					$tour  = Array();
					
					
					while ($match_arr = self::popMatch($used_teams)) {
							foreach ($match_arr as $_m) {
								$used_teams[] = $_m;
							}
							
							$tour[] = $match_arr;
							
					}
					
					$tours[] = $tour;
					
				}
				while (!empty($used_teams));
				
				return $tours;
					
			}
			
			
			private static function genMatchesTable($teams_count) {
				
					self::$teams_count = $teams_count;
					
					$ind = 0;
					
					for($i1 = 1; $i1 <=$teams_count; $i1++)	
					for($i2 = 1; $i2 <=$teams_count; $i2++) {
							if ($i1 != $i2) {
								$ind ++;
								
								$match = Array('owner' => $i1, 'guest' => $i2);
								self::$matches[$ind] = $match;
								
								/*$match = Array('owner' => $i2, 'guest' => $i1);
								self::$matches[$ind + 1] = $match;*/
							}
					}
					
					
					/*foreach($this->matches as $m) {
							foreach ($m as $_m) {
									echo $_m. ' ';
							}
							
							echo '<br>';
					}
					
					die;*/
					
			}
			
			
			public static function PopMatch($used_teams = Array()) {
				
				$_matches = $matches = self::$matches;
				
				foreach($used_teams as $_used_team) {
					foreach ($_matches as $m_ind => $_m) {
						
						foreach ($_m as $_m_team) {
								if ($_used_team == $_m_team) {
									unset($matches[$m_ind]);
									break;
								}
						}						
						
					}
				}
				
				foreach ($matches as $m_index => $_match) {
						unset(self::$matches[$m_index]);
						return $_match;
				}
				
				return false;
			}
			
			
			
		
	}
	
	
	class Tour {
		
		private $games;
		private $matches;
		private $matches_by_team;
		
		public function __construct($games) {
			if (!empty($games)) {
				$this->games = $games;
				$this->play();
			}
			return false;
		}
		
		
		public function getMatches() {
				return $this->matches;
		}
		
		public function getMatchesByTeam() {
				return $this->matches;
		}
		
		
		
		private function play() {
			foreach($this->games as $g) {
				$m = new Match(Teams::get($g['owner']), Teams::get($g['guest']));
				$this->matches_by_team[$g['owner']][] = $m;
				$this->matches_by_team[$g['guest']][] = $m;
				
				$this->matches[] = $m;
			}
			
			Teams::sortTable();
			
		}
		
		
	}
	
	class Match {
		
			private $team1, $team2;
			private $score1, $score2;
			
			public function __construct($team1, $team2) {
					$this->team1 = $team1;
					$this->team2 = $team2;
					
					$this->playGame();
					
			}
			
			public function getResult() {
				return Array('owner' => $this->score1, "guest" => $this->score2);
			}
			
			public function getTeamStatus($team) {

				if (!in_array($team, Array($this->team1, $this->team2)))
					return false;
				
				return ($team == $this->team1) ? 'owner' : 'guest';
			}
			
			
			public function getStatisticByTeam($team) {

				if (!in_array($team, Array($this->team1, $this->team2)))
					return false;
				
				return ($team == $this->team1) ? 1 : 2;
			}
			
			
			public function getTeam1() {
				return $this->team1;
			}
			
			public function getTeam2() {
				return $this->team2;
			}
			
			public function getScore1() {
				return $this->score1;
			}
			
			public function getScore2() {
				return $this->score2;
			}
			
			
			private function playGame() {
				$t1 = $this->team1;
				$t2 = $this->team2;
				
				$this->score1 = (/*($t1->get_rate())* */(rand(1,6))) - 1;
				$this->score2 = (/*($t2->get_rate)* */ (rand(1,6))) - 1;
				
				$t1->addGame($this);
				$t2->addGame($this);
				
				return $this->getResult();
			}
	}
	
	
	
	

	
	class Team {
		
		private $name;
		private $index;
		
		
		private $rate = 0;
		private $chance = 100;
		
		private $scores = 0;
		
		private $wins = 0;
		private $looses = 0;
		private $draws = 0;
		
		
		private $goals_scored = 0;
		private $goals_conceded = 0;
		
		private $no_looses_in_row = 0;
		private $looses_in_row = 0;
		
		private $guest_wins = 0;
		private $home_looses = 0;
		
		private $matches;
		
		private $matches_count = 0;
		
		private $wins_in_row;
		
			
		
		public function __construct($name, $ind) {
			$this->name = $name;
			
			$this->index = (int)$ind;
			
			
		}
		
		
		public function get_rate() {
				return $this->rate;
		}
		
		public function get_matches_count() {
				return $this->matches_count;
		}
		
		public function set_rate($rate) {
				$this->rate = $rate;
		}
		
		public function get_goals_scored() {
				return $this->goals_scored;
		}
		
		public function get_goals_conceded() {
				return $this->goals_conceded;
		}
		
		public function get_name() {
			return $this->name;
		}
		
		
		public function get_scores() {
				return $this->scores;
		}
		
		public function calcRate() {
			
			$rate = 0;
			
			$rate += $this->scores * 20;
			
			$rate += ($this->wins - $this->matches_count) * 10; // wins to all matches
			$rate += ($this->wins + $this->draws - $this->matches_count) * 5; // no looses to all matches
			$rate += ($this->looses - $this->matches_count) * -10; // looses to all matches
			
			
			$rate += ($this->home_looses) * (-10); // home looses matches
			
			
			$rate += ($this->wins - $this->looses)*15; // wins to looses
			
			
			$rate += ($this->wins + $this->draws - $this->looses)*5; // no looses to looses
			
			$rate += $this->wins_in_row * 12; // wins in row
			$rate += $this->no_looses_in_row * 10; // no looses in row
			$rate += $this->guest_wins * 10; // guest wins in row
			
			$rate += ($this->goals_scored - $this->goals_conceded)*10;
			
			
			
			$rate +=  (Teams::get_tours_count() - $this->matches_count)*100;
			
			
			return $rate;
				
				
			
		}
		
		public function calcChances($tour_num, $teams_count, $leader_team) {
			
			$games_count = $teams_count*2 - 2;
			$games_count_to_play = $teams_count*2 - 2 - $tour_num;
			//$games_delta = $games_count - $games_count_to_play;
			
			//$loose_scores = $tour_number*3 - $this->scores;
			
			$max_scores_can_take = $games_count_to_play*3;
			
			//if ($leader_team->scores - $this->scores <= )
			
			
			///$loose_scores_procent = $games_count*3 /$loose_scores;
			
			//$this->scores + $games_delta*3*$loose_scores_procent;
			
		}
		
		
		
		public function addGame($match) {
				
				$t1 = $match->getTeam1();
				$t2 = $match->getTeam2();
				
				
				if (!$status = $match->getTeamStatus($this))
					return false;
				
				if ($status == 'guest') {
					$my_score = $match->getScore2();
					$opp_score = $match->getScore1();
					
					$me = $match->getTeam2();
					$opp = $match->getTeam1();
					
				}
				else {
					$my_score = $match->getScore1();
					$opp_score = $match->getScore2();
					
					
					$me = $match->getTeam1();
					$opp = $match->getTeam2();
				}
					
					$this->goals_scored += $match->getScore1();  
					$this->goals_conceded += $match->getScore2();
					
					if ($my_score > $opp_score) {
							$this->wins++;
							$this->scores += 3;
							$this->no_looses_in_row += 1;
							$this->wins_in_row += 1;
							
							$this->looses_in_row = 0;
							
							if ($status == 'guest') {
								$this->guest_wins++;
							}
					}
					elseif ($my_score < $opp_score) {
							$this->looses++;
							$this->no_looses_in_row += 1;
							$this->looses_in_row = 0;
							$this->wins_in_row = 0;
					}
					else {
							$this->draws++;
							$this->scores += 1;
							
							$this->looses_in_row += 1;
							$this->no_looses_in_row = 0;
							$this->wins_in_row = 0;
							
							
							if ($status == 'home') {
								$this->home_looses++;
							}
					}	
					
				$this->matches[$opp->getIndex()][] = $match;
				
				$this->matches_count++;
				
				$this->rate = $this->calcRate();
				
		}
		
		public function getIndex() {
				return $this->index;
		}

		public function getMatches() {
				return $this->matches;
		}
		
		
		
		 	
		
		
	}
	
	
	class League {
		
		private $matches = Array();
		private $tours = Array();
		private $tours_list = Array();
		
		
		public function __construct($teams) {
			
			/*if (isset($_SESSION['league_games']))
			$this->games = $tours;*/
		
			foreach ($teams as $_team) {
					
					Teams::addTeam(new Team($_team, Teams::get_count()+1));
			}

			$this->tours_list = Match_list::getTours(Teams::get_count());
			
		
			
			
		}
		
		
		public function nextWeek() {
			
			
			$t = new Tour($this->tours_list[count($this->tours)]);
			
			$matches = $t->getMatches() ?? false;
			
			if (!$matches) {
				return false;
			}
			
			$this->matches = array_merge_recursive($this->matches, $matches);
			$this->tours[] = $t;
			
			return $t;
			
		}
		
	}
	
	class PrintOut {
		public static function print_tour($tour) {
				foreach ($tour->getMatches() as $m) {
		
		
					echo $m->getTeam1()->get_name(). ' - ';
					echo $m->getTeam2()->get_name(). ' ';
					
					echo $m->getResult()['owner']. ' : '.$m->getResult()['guest']. '<br>';
				}
				
				
				
	
		}
		
		public static function print_table() {
				echo '<br>'.'==========================<br>';
				foreach (Teams::getTable() as $item) {
						echo $item->get_name(). ' - '.$item->get_scores(). ' - '. $item->get_rate(). ' - ' . $item->get_goals_scored(). ' - '. $item->get_goals_scored() . ' - '. $item->get_goals_conceded(). '<br>';
				}
				echo '<br>'.'==========================<br>';
		}
	}


	//ENTRY POINT
	
	session_start();
	
	
	if (isset($_SESSION['league'])) {
			$lg = $_SESSION['league'];
			
			Teams::restore_from_session();
			
			
	}
	
	else {
		$teams = Array('Man U', 'Barcelona', 'Real', 'Liverpool');
	
		$lg = new League($teams);
		
	}
	
	
	$tour = $lg->nextWeek();
	
	if ($tour)
		PrintOut::print_tour($tour);
	
	PrintOut::print_table();
	
	
	$_SESSION['league'] = $lg;
	Teams::save_to_session();
	
	
	//Teams::
	
	
	
	//
	
	
	
	
	
	

?>