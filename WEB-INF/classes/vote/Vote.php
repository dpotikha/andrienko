<?php 

/**
CREATE TABLE dbm_voting_questions (
	id INTEGER NOT NULL PRIMARY KEY,
	question TEXT NOT NULL,
	is_active SMALLINT NOT NULL
);
CREATE TABLE dbm_voting_answers (
	question_id INTEGER NOT NULL,
	number INTEGER NOT NULL,
	answer TEXT NOT NULL,
	voted_count INTEGER NOT NULL,
	PRIMARY KEY (question_id, number)
);
CREATE TABLE dbm_voting_votes (
	question_id INTEGER NOT NULL,
	answer_number INTEGER NOT NULL,
	ip_addr VARCHAR(15) NOT NULL,
	PRIMARY KEY (question_id, ip_addr)
);
*/

function checkIfUserAlreadyVoted($question) {
	$ip = getUniqueClientIPString();
	$cookieName = "vote" . $question->id;
	$cookie = $_COOKIE[$cookieName];
	$alreadyVoted = $cookie == "voted";

	if (!$alreadyVoted) {
		$alreadyVoted = $question->checkIPAlreadyVoted($ip);
	}

	return $alreadyVoted;
}

function getUniqueClientIPString() {
	$ip = $_SERVER['REMOTE_ADDR'];
	$realIP = getClientIP();

	if ($ip != $realIP) {
		return $ip . "_" . $realIP;
	} else {
		return $ip;
	}
}

class Question {
	var $id = 0;
	var $isActive = false;
	var $question = "";
	var $answers = array();
	var $answer_vote_counts = array();
	var $answer_vote_real_counts = array();

	function __construct() {
	}

	function _load(&$rs) {
		global $connection;

		$this->id = $rs->getInt("id");
		$this->isActive = $rs->getInt("is_active") == 1;
		$this->question = $rs->getString("question");

		$answersQuery = "SELECT * FROM dbm_voting_answers WHERE question_id=".$this->id." ORDER BY number";
		$answersVotesCountQuery = "SELECT COUNT(*) FROM dbm_voting_votes WHERE question_id=".$this->id." AND answer_number=";
		$answersRS = DBUtils::execSelect($answersQuery);
		$i=0;
		while ($answersRS->next()) {
			$this->answers[$i] = $answersRS->getString("answer");
			$this->answer_vote_counts[$i] = $answersRS->getInt("voted_count");
			$this->answer_vote_real_counts[$i] = DBUtils::execCountSelect($answersVotesCountQuery . $i);
			$i++;
		}
	}

	function checkIPAlreadyVoted($ip) {
		$query = "SELECT COUNT(*) FROM dbm_voting_votes WHERE ip_addr=? AND question_id=?";
		$count = DBUtils::execCountSelect($query, array($ip, $this->id));
		return $count == 1;
	}

	function getAnswersCount() {
		return sizeof($this->answers);
	}

	function getTotalVotesCount() {
		$totalCount = 0;
		for ($i=0; $i < sizeof($this->answers); $i++) {
			$totalCount += $this->answer_vote_counts[$i];
		}
		return $totalCount;
	}

	function getTotalRealVotesCount() {
		$totalCount = 0;
		for ($i=0; $i < sizeof($this->answers); $i++) {
			$totalCount += $this->answer_vote_real_counts[$i];
		}
		return $totalCount;
	}

	function& createNew($question, $isActive = false) {
		$query = "INSERT INTO dbm_voting_questions (id, question, is_active) VALUES (?, ?, ?)";
		$newID = DBUtils::getNextSequnceID("dbm_voting_questions");
		DBUtils::execUpdate($query, array($newID, $question, 0));

		$question = new Question();
		$question->id = $newID;
		$question->question = $question;
		$question->isActive = false;
		if ($isActive) {
			$question->setActive();
		}
		return $question;
	}

	function setActive() {
		$query = "UPDATE dbm_voting_questions SET is_active=0 WHERE id!=".$this->id;
		DBUtils::execUpdate($query);
		$query = "UPDATE dbm_voting_questions SET is_active=1 WHERE id=".$this->id;
		DBUtils::execUpdate($query);

		$this->isActive = true;
	}

	function addAnswer($answer) {
		$query = "INSERT INTO dbm_voting_answers (question_id, number, answer, voted_count) VALUES (?, ?, ?, ?)";
		DBUtils::execUpdate($query, array($this->id, sizeof($this->answers), $answer, 0));
		$this->answers[] = $answer;
		$this->answer_vote_counts[] = 0;
		$this->answer_vote_real_counts[] = 0;
	}

	/**
	 * Return true if OK or false if given IP has already voted
	 */
	function vote($answerNumber, $ip) {
		if ($this->checkIPAlreadyVoted($ip)) {
			return false;
		} else {
			$query = "INSERT INTO dbm_voting_votes (question_id, answer_number, ip_addr) VALUES (?, ?, ?)";
			DBUtils::execUpdate($query, array($this->id, $answerNumber, $ip));

			$query = "UPDATE dbm_voting_answers SET voted_count=voted_count+1 WHERE question_id=? AND number=?";
			DBUtils::execUpdate($query, array($this->id, $answerNumber));

			$this->answer_vote_counts[$answerNumber]++;
			$this->answer_vote_real_counts[$answerNumber]++;

			return true;
		}
	}

	/**
	 * FOR ADMIN USE ONLY
	 */
	function setVotedCount($answerNumber, $count) {
		$query = "UPDATE dbm_voting_answers SET voted_count=? WHERE question_id=? AND number=?";
		DBUtils::execUpdate($query, array($count, $this->id, $answerNumber));

		$this->answer_vote_counts[$answerNumber]++;
	}

	function remove() {
		if ($this->isActive) {
			$questions = Question::findAll();
			for ($i=0; $i < sizeof($questions); $i++) {
				$question = &$questions[$i];
				if ($question->id == $this->id) {
					continue;
				}
				$question->setActive();
				break;
			}
		}

		$query = "DELETE FROM dbm_voting_questions WHERE id=?";
		DBUtils::execUpdate($query, array($this->id));

		$query = "DELETE FROM dbm_voting_answers WHERE question_id=?";
		DBUtils::execUpdate($query, array($this->id));

		$query = "DELETE FROM dbm_voting_votes WHERE question_id=?";
		DBUtils::execUpdate($query, array($this->id));
	}

	function removeAnswer($answerNumber) {
		$query = "DELETE FROM dbm_voting_answers WHERE question_id=? AND number=?";
		DBUtils::execUpdate($query, array($this->id, $answerNumber));

		$query = "DELETE FROM dbm_voting_votes WHERE question_id=? AND answer_number=?";
		DBUtils::execUpdate($query, array($this->id, $answerNumber));

		for ($i = $answerNumber+1; $i < sizeof($this->answers); $i++) {
			$this->answers[$i-1] = $this->answers[$i];
			$this->answer_vote_counts[$i-1] = $this->answer_vote_counts[$i];
			$this->answer_vote_real_counts[$i-1] = $this->answer_vote_real_counts[$i];

			$query = "UPDATE dbm_voting_answers SET number=? WHERE question_id=? AND number=?";
			DBUtils::execUpdate($query, array($i-1, $this->id, $i));

			$query = "UPDATE dbm_voting_votes SET answer_number=? WHERE question_id=? AND answer_number=?";
			DBUtils::execUpdate($query, array($i-1, $this->id, $i));
		}
	}

	function storeAll() {
		$query = "UPDATE dbm_voting_questions SET question=? WHERE id=?";
		DBUtils::execUpdate($query, array($this->question, $this->id));

		$query = "UPDATE dbm_voting_answers SET answer=?, voted_count=? WHERE question_id=? AND number=?";
		for ($i = 0; $i < sizeof($this->answers); $i++) {
			DBUtils::execUpdate($query, array($this->answers[$i], $this->answer_vote_counts[$i], $this->id, $i));
		}
	}

	function& findAll() {
		$query = "SELECT * FROM dbm_voting_questions ORDER BY id";
		$rs = DBUtils::execSelect($query);
		$questions = array();
		while($rs->next()) {
			$question = new Question();
			$question->_load($rs);
			$questions[] = &$question;
		}
		return $questions;
	}


	function& findAllExceptOne($id) {
		$query = "SELECT * FROM dbm_voting_questions WHERE id!=? ORDER BY id";
		$rs = DBUtils::execSelect($query, array($id));
		$questions = array();
		while($rs->next()) {
			$question = new Question();
			$question->_load($rs);
			$questions[] = &$question;
		}
		return $questions;
	}

	function& findById($id) {
		$query = "SELECT * FROM dbm_voting_questions WHERE id=" . $id;
		$rs = DBUtils::execSelect($query);
		$question = null;
		if ($rs->next()) {
			$question = new Question();
			$question->_load($rs);
		}
		return $question;
	}

	function& findActive() {
		$query = "SELECT * FROM dbm_voting_questions WHERE is_active=1";
		$rs = DBUtils::execSelect($query);
		$question = null;
		if ($rs->next()) {
			$question = new Question();
			$question->_load($rs);
		}
		return $question;
	}
}

?>