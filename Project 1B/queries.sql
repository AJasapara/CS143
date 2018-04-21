SELECT CONCAT(Actor.first, ' ',Actor.last) AS actor_name FROM Actor, MovieActor, Movie WHERE Movie.title = 'Die Another Day' AND Movie.id = MovieActor.mid AND Actor.id = MovieActor.aid;
-- The names of all the actors in the movie 'Die Another Day'.

SELECT COUNT(count) FROM (SELECT COUNT(mid) as count FROM MovieActor GROUP BY aid HAVING COUNT(mid) > 1) as temp;
-- The count of all the actors who acted in multiple movies.

SELECT DISTINCT MovieGenre.genre, Movie.rating, COUNT(Movie.id) as movie_count FROM Movie, MovieGenre WHERE Movie.id = MovieGenre.mid GROUP BY MovieGenre.genre, Movie.rating;
-- The count of all the movies in each genre broken down by rating (interesting to see spread of distribution of ratings per genre such as 'Horror' having majority 'R' ratings).