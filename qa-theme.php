<?php
/*
	'ClearCobalt' theme for Question2Answer by Ansgar Wiechers,
  based on the theme 'Clean Base' by Scott Vivian.

	-----------------------------------------------------------------------

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	More about this license: http://www.gnu.org/licenses/gpl.html
*/

class qa_html_theme extends qa_html_theme_base
{
	private $favourite;

	function doctype()
	{
		// use standards doctype
		$this->output( '<!DOCTYPE html>' );
	}

	function head_title()
	{
		// create unique page titles on paginated sections
		if ( qa_get('start') && isset($this->content['title']) )
			$this->content['title'] = $this->content['title'] . ' (page ' . floor(qa_get('start') / $this->_get_per_page() + 1) . ')';

		parent::head_title();
	}

	function head_metas()
	{
		// set viewport for responsive layout
		$this->output('<meta name="viewport" content="width=device-width, initial-scale=1.0">');
	}

	function page_title_error()
	{
		if ( isset($this->content['q_view']['url']) )
		{
			$this->content['title'] = '<a href="' . $this->content['q_view']['url'] . '">' . @$this->content['title'] . '</a>';
			if ( @$this->content['q_view']['raw']['closedbyid'] !== null )
				$this->content['title'] .= ' [closed]';
		}

		// remove favourite star here
		$this->favourite = @$this->content['favorite'];
		unset($this->content['favorite']);

		if ( $this->template != 'question' && isset($this->favourite) ) {
			$this->output('<DIV CLASS="qa-favoriting" '.@$this->favourite['favorite_tags'].'>');
			$this->favorite_inner_html($this->favourite);
			$this->output('</DIV>');
		}

		parent::page_title_error();
	}

	function q_item_stats($question)
	{
		$this->output('<DIV CLASS="qa-q-item-stats">');

		$this->voting($question);
		$this->a_count($question);
		$this->view_count($question);

		$this->output('</DIV>');
	}

	function q_item_main($question)
	{
		$this->output('<DIV CLASS="qa-q-item-main">');

		$this->q_item_title($question);
		$this->post_avatar($question, 'qa-q-item');
		$this->post_meta($question, 'qa-q-item');
		$this->post_tags($question, 'qa-q-item');

		$this->output('</DIV>');
	}

	function q_item_title($q_item)
	{
		// display "closed" message in question list
		$closed = @$q_item['raw']['closedbyid'] !== null;

		$this->output(
			'<DIV CLASS="qa-q-item-title">',
			'<A HREF="'.$q_item['url'].'">'.$q_item['title'].'</A>',
			($closed ? ' [closed] ' : ''),
			'</DIV>'
		);
	}

	public function a_selection($post) {
		$this->output('<div class="qa-a-selection">');

		if (isset($post['select_tags'])) {
			$this->post_hover_button($post, 'select_tags', '', 'qa-a-select');
		} elseif (isset($post['unselect_tags'])) {
			$this->post_hover_button($post, 'unselect_tags', '', 'qa-a-unselect');
		} elseif ($post['selected']) {
			$this->output('<div class="qa-a-selected">&nbsp;</div>');
		}

		$this->output('</div>');
	}

	function voting($post)
	{
		if (isset($post['vote_view'])) {

			if ( $this->template == 'question' )
				$this->output('<div style="float:left; width:56px">');

			$this->output('<DIV CLASS="qa-voting '.(($post['vote_view']=='updown') ? 'qa-voting-updown' : 'qa-voting-net').'" '.@$post['vote_tags'].' >');
			$this->voting_inner_html($post);
			$this->output('</DIV>');

			if ( $this->template == 'question' )
			{
				// add favourite star back
				if ( $post['raw']['type'] == 'Q' && isset($this->favourite) )
				{
					$this->output('<DIV style="text-align:center" '.@$this->favourite['favorite_tags'].'>');
					$this->favorite_inner_html($this->favourite);
					$this->output('</DIV>');
				}
				$this->view_count($post);
			}

			if ( $this->template == 'question' )
				$this->output('</div>');
		}
	}

	function voting_inner_html($post)
	{
		$this->vote_button_up($post);
		$this->vote_count($post);
		$this->vote_button_down($post);
		$this->vote_clear();
	}

	function vote_button_up($post)
	{
		$this->output('<DIV CLASS="qa-vote-buttons '.(($post['vote_view']=='updown') ? 'qa-vote-buttons-updown' : 'qa-vote-buttons-net').'">');

		switch (@$post['vote_state'])
		{
			case 'voted_down':
			case 'voted_down_disabled':
				break;
			case 'voted_up':
				$this->post_hover_button($post, 'vote_up_tags', '', 'qa-vote-one-button qa-voted-up');
				break;
			case 'voted_up_disabled':
				$this->post_disabled_button($post, 'vote_up_tags', '', 'qa-vote-one-button qa-vote-up');
				break;
			case 'up_only':
			case 'enabled':
				$this->post_hover_button($post, 'vote_up_tags', '', 'qa-vote-first-button qa-vote-up');
				break;
			default:
				$this->post_disabled_button($post, 'vote_up_tags', '', 'qa-vote-first-button qa-vote-up');
				break;
		}

		$this->output('</DIV>');
	}

	function vote_button_down($post)
	{
		$this->output('<DIV CLASS="qa-vote-buttons '.(($post['vote_view']=='updown') ? 'qa-vote-buttons-updown' : 'qa-vote-buttons-net').'">');

		switch (@$post['vote_state'])
		{
			case 'voted_up':
			case 'voted_up_disabled':
				break;
			case 'voted_down':
				$this->post_hover_button($post, 'vote_down_tags', '', 'qa-vote-one-button qa-voted-down');
				break;
			case 'voted_down_disabled':
				$this->post_disabled_button($post, 'vote_down_tags', '', 'qa-vote-one-button qa-vote-down');
				break;
			case 'enabled':
				$this->post_hover_button($post, 'vote_down_tags', '', 'qa-vote-second-button qa-vote-down');
				break;
			default:
				$this->post_disabled_button($post, 'vote_down_tags', '', 'qa-vote-second-button qa-vote-down');
				break;
		}

		$this->output('</DIV>');
	}

	function vote_count($post)
	{
		if ($post['raw']['type'] == 'C' and $post['upvotes_raw'] == '0' and $post['downvotes_raw'] == '0') {
			$post['netvotes_view']['data'] = '&nbsp;';
		} else {
			$post['netvotes_view']['data'] = str_replace( '+', '', $post['netvotes_view']['data'] );
		}
		parent::vote_count($post);
	}

	function a_count($post)
	{
		// You can also use $post['answers_raw'] to get a raw integer count of answers

		$extraclass = null;
		if ( @$post['answers_raw'] == 0 )
			$extraclass = 'qa-a-count-zero';
		if ( @$post['answer_selected'] )
			$extraclass = 'qa-a-count-selected';

		$this->output_split(@$post['answers'], 'qa-a-count', 'SPAN', 'SPAN', $extraclass);
	}

	public function c_item_main($c_item) {
		if (isset($c_item['main_form_tags'])) {
			$this->output('<form ' . $c_item['main_form_tags'] . '>'); // form for buttons on comment
		}

		$this->error(@$c_item['error']);

		$this->c_item_content($c_item);
		$this->output('<div class="qa-c-item-footer">');
		$this->c_item_buttons($c_item);
		$this->output('</div>');

		if (isset($c_item['main_form_tags'])) {
			$this->form_hidden_elements(@$c_item['buttons_form_hidden']);
			$this->output('</form>');
		}
	}

	public function c_item_content($c_item) {
		if (!isset($c_item['content'])) {
			$c_item['content'] = '';
		}

		$is_updated  = !empty($c_item['raw']['updated']);
		$c_timestamp = $is_updated ? $c_item['raw']['updated'] : $c_item['raw']['created'];

		$this->output('<div class="qa-c-item-content qa-post-content">');
		$this->output($c_item['content']);
		$this->output('&ndash; <span class="qa-c-item-avatar-meta">' . $c_item['who']['data'] . '</span>');
		$this->output('<a href="#' . $c_item['raw']['postid'] . '">');
		$this->_format_timestamp($c_timestamp, 'qa-c-item');
		$this->output('</a>' . ($is_updated ? '<span class="qa-cc-c-item-edited">&#9997;</span>' : ''));
		$this->output('</div>');
	}

	function finish() {} // override indentation comment

	public function avatar($item, $class, $prefix = null) {
		if (isset($item['avatar'])) {
			if (isset($prefix))
				$this->output($prefix);

			$this->output('<div class="' . $class . '-avatar">', $item['avatar'], '</div>');
		}
	}

	public function post_avatar_meta($post, $class, $avatarprefix = null, $metaprefix = null, $metaseparator = '<br/>') {
		$this->post_meta($post, $class, $metaprefix, $metaseparator);
		$this->output('<div class="' . $class . '-avatar-meta qa-cc-avatar-meta">');
		$this->avatar($post, $class, $avatarprefix);
		$this->post_meta_who($post, $class);
		$this->output('</div>');
	}

	public function post_meta($post, $class, $prefix = null, $separator = '<br/>') {
		$this->output('<div class="' . $class . '-meta">');

		if (isset($prefix))
			$this->output($prefix);

		$order = explode('^', @$post['meta_order']);

		foreach ($order as $element) {
			switch ($element) {
				case 'what':
					$this->post_meta_what($post, $class);
					break;
				case 'when':
					$this->post_meta_when($post, $class);
					break;
				case 'where':
					$this->post_meta_where($post, $class);
					break;
				case 'who':
					if ($class === 'qa-q-item') {
						$this->post_meta_who($post, $class);
					}
					break;
			}
		}

		$this->post_meta_flags($post, $class);

		if (!empty($post['what_2']) && $post['what_2'] != 'selected') {
			$this->output($separator);

			foreach ($order as $element) {
				switch ($element) {
					case 'what':
						$this->output('<span class="' . $class . '-what">' . $post['what_2'] . '</span>');
						break;
					case 'when':
						$this->_format_timestamp($post['raw']['updated'], $class);
						break;
					case 'who':
						$this->post_meta_who($post, $class);
						break;
				}
			}
		}

		$this->output('</div>');
	}

	function post_meta_who($post, $class) {
		if (isset($post['who'])) {
			$this->output('<div class="' . $class . '-who">');

			if (strlen(@$post['who']['prefix']))
				$this->output('<span class="' . $class . '-who-pad">' . $post['who']['prefix'] . '</span>');

			if (isset($post['who']['data']))
				$this->output('<span class="' . $class . '-who-data">' . $post['who']['data'] . '</span>');

			if (isset($post['who']['title']))
				$this->output('<span class="' . $class . '-who-title">' . $post['who']['title'] . '</span>');

			// You can also use $post['level'] to get the author's privilege level (as a string)

			if (isset($post['who']['points']) && $post['raw']['type'] != 'C') {
				$post['who']['points']['prefix'] = '('.$post['who']['points']['prefix'];
				$post['who']['points']['suffix'] = ')'; // remove 'points' text

				// show zero for all negative points
				$post['who']['points']['data'] = max($post['who']['points']['data'],0);
				$this->output_split($post['who']['points'], $class . '-who-points');
			}

			if (strlen(@$post['who']['suffix'])) {
				$this->output('<span class="' . $class . '-who-pad qa-cc-who-suffix">');
				if ($class === 'qa-q-view' || $class === 'qa-a-item') {
					$this->output('<br/>');
				}
				$this->output($post['who']['suffix'] . '</span>');
			}

			$this->output('</div>');
		}
	}

	function post_meta_when($post, $class) {
		if (array_key_exists('otime', $post['raw']) and $post['raw']['_type'] !== 'Q') {
			$this->_format_timestamp($post['raw']['otime'], $class);
		} else {
			$this->_format_timestamp($post['raw']['created'], $class);
		}
	}

	private function _format_timestamp($timestamp, $class) {
		$interval     = qa_opt('db_time') - $timestamp;
		$fulldatedays = qa_opt('show_full_date_days');

		if ($interval < 0 || (isset($fulldatedays) && $interval > 86400 * $fulldatedays)) {
			$gmdate = gmdate('Y-m-d H:i:s e', $timestamp);
			$post_when = array(
				'data' => '<time itemprop="dateCreated" datetime="' . $gmdate . '" title="' . $gmdate . '">' . gmdate('Y-m-d', $timestamp) . '</time>',
			);
		} else {
			// ago-style date
			$post_when = qa_lang_html_sub_split('main/x_ago', qa_html(qa_time_to_string($interval)));
		}

		$this->output_split($post_when, $class . '-when');
	}

	private function _get_per_page()
	{
		$arr = array('page_size_qs', 'page_size_tags', 'page_size_users', 'page_size_search');
		$options = qa_get_options($arr);

		switch ( $this->template )
		{
			case 'questions':
				return $options['page_size_qs'];
			case 'tags':
				return $options['page_size_tags'];
			case 'users':
				return $options['page_size_users'];
			case 'search':
				return $options['page_size_search'];
		}

		return 20;
	}

	private function _debug( $var )
	{
		$dump = $var ? print_r($var, true) : 'NULL';
		$this->output( '<pre>'.$dump.'</pre>' );
	}

}
