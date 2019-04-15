<?php

function score_recalc($challenge) {

    $points = scores_dynamic_score($challenge);

    db_update(
          'challenges',
          array(
             'points' => $points
          ),
          array(
             'id' => $challenge
          )
        );

    return $points;

}

function scores_dynamic_score($challenge) {

    $ch = db_query_fetch_one('
        SELECT
           ch.points,
           ch.dynamic_score,
           ch.dyn_max_points,
           ch.dyn_min_points,
           ch.dyn_threshold
        FROM challenges AS ch
        WHERE
           ch.id = :id',
        array(
           'id' => $challenge)
    );

    if (!$ch['dynamic_score']) {
      return $ch['points'];
    }

    $submissions = db_query_fetch_all(
        'SELECT
            u.id AS user_id
          FROM users AS u
          LEFT JOIN submissions AS s ON s.user_id = u.id
          WHERE
             u.competing = 1 AND
             s.challenge = :id AND
             s.correct = 1
          ORDER BY s.added ASC',
        array('id' => $challenge)
    );

    $max = (int) $ch['dyn_max_points'];
    $min = (int) $ch['dyn_min_points'];
    $thr = (float) $ch['dyn_threshold'];
    $solves = count($submissions);

    if($solves<=1) {
      return $max;
    }

  $dyn = (int) (((($min - $max)/($thr**2)) * ($solves**2)) + $max);

    return max($dyn, $min);
}
