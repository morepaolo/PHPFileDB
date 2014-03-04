select name, second(now()), now() as adesso, dayofweek('2007-02-03'), dayofmonth('2007-02-03'), dayofyear('2007-02-03') from prova;

delete from cache;