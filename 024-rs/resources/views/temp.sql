SELECT year, week_no, min(month) as month, min(day) as day FROM `CALENDAR` WHERE 1 GROUP BY year, week_no , CONCAT(year, lpad(month, 2, '0'), lpad(day, 2, '0'))

SELECT year, week_no, min(CONCAT(year,'年', lpad(month, 2, '0'),'月', lpad(day, 2, '0'),'日')) as day FROM `CALENDAR` WHERE 1 GROUP BY year, week_no 

//ビュー作成
CREATE OR REPLACE VIEW WEEK_NO AS SELECT year, week_no, min(CONCAT(year,'年', lpad(month, 2, '0'),'月', lpad(day, 2, '0'),'日')) as day FROM `CALENDAR` WHERE 1 GROUP BY year, week_no ;


SELECT user.name, user.user_no, week_no.day, status.status 
FROM `REPORT_W` as report_w
LEFT OUTER JOIN `USER` as user ON
report_w.id = user.id
LEFT OUTER JOIN `STATUS` as status ON
report_w.status_id = status.status_id
LEFT OUTER JOIN `WEEK_NO` as week_no ON
report_w.week_no = week_no.week_no
ORDER BY user.user_no ASC, week_no.day DESC