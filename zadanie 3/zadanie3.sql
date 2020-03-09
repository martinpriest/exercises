UPDATE user_log AS t
INNER JOIN (
  SELECT id, user_id, MAX(saved) AS sav
  FROM user_log
  WHERE action_type = "abuse"
  GROUP BY user_id
) AS t1
ON t1.sav = t.saved
SET notify_admin = 1;

SELECT * FROM user_log
WHERE notify_admin = 1;