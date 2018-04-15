create table Movie(id int, title varchar(100), year int, rating varchar(10), company varchar(50));
-- id = Movie ID, title = Movie title, year = Release year, rating = MPAA rating, company = Production company

create table Actor(id int, last varchar(20), first varchar(20), sex varchar(6), dob date, dod date);
-- id = Actor ID, last = Last name, first = First name, sex = Sex of the actor, dob = Date of birth, dod = Date of death

create table Director(id int, last varchar(20), first varchar(20), dob date, dod date);
-- id = Director ID, last = Last name, first = First name, dob = Date of birth, dod = Date of death

create table MovieGenre(mid int, genre varchar(20));
-- mid = Movie ID, genre = Movie genre

create table MovieDirector(mid int, did int);
-- mid = Movie ID, did = Director ID

create table MovieActor(mid int, aid int, role varchar(50));
-- mid = Movie ID, aid = Actor ID, role = Actor role in movie

create table Review(name varchar(20), time timestamp, mid int, rating int, comment varchar(500));
-- name = Reviewer name, time = Review time, mid = Movie ID, rating = Review rating, comment = Review comment

create table MaxPersonID(id int);
-- id = Max ID assigned to all persons

create table MaxMovieID(id int);
-- id = Max ID assigned to all movies