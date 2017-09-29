update wp.wp_term_relationships set term_taxonomy_id = 1;
update wp.wp_terms set id=id+(select max(id) from nofrag_old.games);
insert into wp_terms(`term_id`, `name`, `slug`) 
select id,name,shortname from nofrag_old.games;
insert into wp.wp_term_relationships values(
	select wpposts.id, oldnews.game_id 
	from wp.wp_posts wpposts, nofrag_old.news oldnews, wp.wp_transpo_news transponews
	where wpposts.id = transponews.news_id 
	and oldnews.news_id = transponews.old_news_id
	and oldnews.game_id > 0
)


update wp.wp_posts set id = (
	select old_news_id from wp.wp_transpo_news where news_id = id)