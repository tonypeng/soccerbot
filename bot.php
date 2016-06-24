<?php

class Bot {
    private $fb;
    private $db;
    private $groupFBID;

    public static function extendAccessToken(string $access_token): string {
        $fb = new \Facebook\Facebook([
            'app_id' => Config::FB_APP_ID,
            'app_secret' => Config::FB_APP_SECRET,
            'default_graph_version' => 'v2.6',
        ]);

        $OAuth2_client = $fb->getOAuth2Client();
        $long_lived_access_token = $OAuth2_client->getLongLivedAccessToken($access_token);
        return $long_lived_access_token;
    }

    public function __construct(string $access_token, string $group_id) {
        $this->fb = new \Facebook\Facebook([
            'app_id' => Config::FB_APP_ID,
            'app_secret' => Config::FB_APP_SECRET,
            'default_graph_version' => 'v2.6',
            'default_access_token' => $access_token,
        ]);
        vprint('Testing connection...');
        $resp = $this->fb->get('/me');
        if ($resp->getGraphUser()) {
            vprintln('OK');
        } else {
            vprintln('FAILED');
            throw new Exception('Invalid access token.');
        }
        $this->groupFBID = $group_id;
        vprintln('Created bot for group '.$group_id.'.');
        $this->db = Database::get();
        vprintln('');
    }

	public function runOneCycle() {
        vprintln('Running one cycle...');
        vprintln('--------------------');
        $resp = $this->fb->get('/'.$this->groupFBID.'/feed?fields=message,from');
	    $feed = $resp->getGraphEdge();
        if (!$feed) {
            throw new Exception('Error fetching group feed.');
        }
        $posts = $feed->asArray();
//        while($resp = $this->fb->next($feed)) {
//            $posts = array_merge($posts, $resp->asArray());
//        }
        foreach ($posts as $post) {
            $this->processPost($post);
        }
        vprintln('--------------------');
        vprintln('');
    }

    private function processPost($post) {
        if(!isset($post['id']) || !isset($post['message'])) {
            return;
        }

        $stmt = $this->db->prepare('SELECT COUNT(*) FROM tilted_posts WHERE post_fbid=:p');
        $stmt->bindParam(':p', $post['id']);
        $stmt->execute();
        $row_count = $stmt->fetchColumn(0);
        if ($row_count > 0) {
            // already tilted
            return;
        }

        list($min_count) = $this->getPostParameters($post);
        vprintln('Processing post '.$post['id'].'.');
        vprintln('Minimum people: '.$min_count);

        // process comments for commands
        $resp = $this->fb->get('/'.$post['id'].'/comments');
        $comments_edge = $resp->getGraphEdge();
        $comments = $comments_edge->asArray();
        while ($comments_edge = $this->fb->next($comments_edge)) {
            $comments = array_merge($comments, $comments_edge->asArray());
        }
        // post author is always in
        $people_in = [$post['from']['id'] => $post['from']['name']];
        foreach ($comments as $comment) {
            list($in) = $this->processPostComment($comment);
            if ($in) {
                list ($user_fbid, $user_name) = $in;
                $people_in[$user_fbid] = $user_name;
            }
        }

        $in_count = count($people_in);
        vprintln('In: '.$in_count);
        if ($in_count >= $min_count) {
            vprintln('Threshold reached!');
            $params = [
                'message' => 'Game on! '.$in_count.' people are in: '.natural_language_join($people_in),
            ];
            $this->fb->post('/'.$post['id'].'/comments', $params);
            $stmt = $this->db->prepare('INSERT INTO tilted_posts (post_fbid) VALUES (:p)');
            $stmt->bindParam(':p', $post['id']);
            $stmt->execute();
        }
        vprintln('');
    }

    private function getPostParameters($post) {
        $message = $post['message'];
        $hashtags = extract_hash_tags($message);
        $min_people = 8;
        foreach ($hashtags as $hashtag) {
            if (startsWith($hashtag, 'need')) {
                $min_people = (int)$this->getCommandArgs($hashtag, 'need');
            }
        }
        return [$min_people];
    }

    private function processPostComment($comment): array {
        if (!isset($comment['message'])) {
            return [false];
        }
        $message = $comment['message'];
        $hashtags = extract_hash_tags($message);
        $in = null;
        foreach ($hashtags as $hashtag) {
            if ($hashtag === 'in') {
                $in = [$comment['from']['id'], $comment['from']['name']];
            }
        }
        return [$in];
    }

    private function getCommandArgs(string $str, string $command) {
        return mb_substr($str, mb_strlen($command));
    }
}