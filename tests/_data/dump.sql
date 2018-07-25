
CREATE TABLE `rating` (
  `rating_id` int(11) NOT NULL,
  `respondent_id` int(11) NOT NULL COMMENT 'Respondent',
  `survey_id` int(11) NOT NULL COMMENT 'Survey',
  `sample_id` int(11) NOT NULL COMMENT 'Sample',
  `value` int(11) NOT NULL COMMENT 'Rating value',
  `comment` text DEFAULT NULL COMMENT 'Comment',
  `user_created` int(11) NOT NULL DEFAULT 1 COMMENT 'Created by',
  `user_updated` int(11) NOT NULL DEFAULT 1 COMMENT 'Updated by',
  `user_closed` int(11) DEFAULT NULL COMMENT 'Closed by',
  `time_created` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Created at',
  `time_updated` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Updated at',
  `time_closed` datetime NOT NULL DEFAULT '3000-12-31 00:00:00' COMMENT 'Closed at'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `rating` (`rating_id`, `respondent_id`, `survey_id`, `sample_id`, `value`, `comment`, `user_created`, `user_updated`, `user_closed`, `time_created`, `time_updated`, `time_closed`) VALUES
(1, 145, 2, 3, 4, 'sdfadsf', 1, 1, NULL, '2018-05-17 16:04:20', '2018-05-17 16:04:20', '3000-12-31 00:00:00');

--
ALTER TABLE `rating`
  ADD PRIMARY KEY (`rating_id`),
  ADD KEY `fk_rating_sample_id` (`respondent_id`),
  ADD KEY `ix_value` (`value`);

ALTER TABLE `rating`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `rating`
  ADD CONSTRAINT `fk_rating_respondent_id` FOREIGN KEY (`respondent_id`) REFERENCES `respondent` (`respondent_id`),
  ADD CONSTRAINT `fk_rating_sample_id` FOREIGN KEY (`respondent_id`) REFERENCES `respondent` (`respondent_id`),
  ADD CONSTRAINT `fk_rating_survey_id` FOREIGN KEY (`respondent_id`) REFERENCES `respondent` (`respondent_id`);
COMMIT;
