CREATE TABLE IF NOT EXISTS category(
    codcategory SERIAL NOT NULL PRIMARY KEY,
    categoryname VARCHAR(50),
    categorypath VARCHAR(100) UNIQUE
);;

CREATE TABLE IF NOT EXISTS author(
    codauthor SERIAL NOT NULL PRIMARY KEY,
    userid VARCHAR(40),
    username VARCHAR(30),
    usericon VARCHAR(80),
    type VARCHAR(150)
);;

CREATE TABLE IF NOT EXISTS stats(
    codstats SERIAL NOT NULL PRIMARY KEY,
    comments INTEGER,
    favourites INTEGER
);;

CREATE TABLE IF NOT EXISTS content(
    codcontent SERIAL NOT NULL PRIMARY KEY,
    src VARCHAR(150),
    filesize INTEGER,
    height INTEGER,
    width INTEGER,
    istransparency BOOLEAN
);;

CREATE TABLE IF NOT EXISTS deviation(
    coddeviation SERIAL NOT NULL PRIMARY KEY,
    deviationid VARCHAR(40),
    printid VARCHAR(40),
    url VARCHAR(150),
    title VARCHAR(100),
    codcategory INTEGER references category(codcategory),
    isdownloadable BOOLEAN,
    ismature BOOLEAN,
    isfavourited BOOLEAN,
    isdeleted BOOLEAN,
    codauthor INTEGER references author(codauthor),
    codstats INTEGER references stats(codstats),
    publishedtime INTEGER,
    isallowcomments BOOLEAN,
    codcontent INTEGER references content(codcontent)
);;

CREATE TABLE IF NOT EXISTS tag(
    codtag SERIAL NOT NULL PRIMARY KEY,
    tagname VARCHAR(200) NOT NULL,
    sponsored BOOLEAN,
    sponsor VARCHAR(200),
    coddeviation INTEGER references deviation(coddeviation)
);;

CREATE TABLE IF NOT EXISTS thumb(
    codthumb SERIAL NOT NULL PRIMARY KEY,
    coddeviation INTEGER references deviation(coddeviation),
    src VARCHAR(250),
    height INTEGER,
    width INTEGER,
    istransparency BOOLEAN
);;

CREATE TABLE IF NOT EXISTS preview(
    codpreview SERIAL NOT NULL PRIMARY KEY,
    src VARCHAR(250),
    height INTEGER,
    width INTEGER,
    istransparency BOOLEAN
);;

DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'usertype') THEN
        CREATE TYPE usertype AS ENUM
        (
            'client',
            'administrator'
        );
    END IF;
    --more types here...
END$$; ;;

CREATE TABLE IF NOT EXISTS preview(
    codpreview SERIAL NOT NULL PRIMARY KEY,
    src VARCHAR(250),
    height INTEGER,
    width INTEGER,
    istransparency BOOLEAN
);;

CREATE TABLE IF NOT EXISTS userdata(
    username VARCHAR(100) NOT NULL PRIMARY KEY,
    password VARCHAR(100) NOT NULL,
    email VARCHAR(250) NOT NULL UNIQUE,
    usertype usertype
);;

CREATE TABLE IF NOT EXISTS userDeviation(
    coduserdeviation SERIAL NOT NULL PRIMARY KEY,
    coduser VARCHAR(100) references userdata(username),
    coddeviation INTEGER references deviation(coddeviation)
);;

CREATE TABLE IF NOT EXISTS userUser(
    coduseruser SERIAL NOT NULL PRIMARY KEY,
    codUser1 VARCHAR(100) references userdata(username),
    codUser2 VARCHAR(100) references userdata(username)
);;

DO $$
BEGIN
    IF NOT EXISTS (SELECT codtag FROM tag WHERE tagname = 'NoTag') THEN
        INSERT INTO tag VALUES(default, 'NoTag', false, '');
    END IF;
    --more types here...
END$$;

