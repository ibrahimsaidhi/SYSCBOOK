import mysql.connector


def submit_sql(filename: str):
    """ Submits the results retreived from the .sql files students submitted
    """
    global database_name
    # establish a connection to the database
    cnx = mysql.connector.connect(user='root', password='', host='localhost')
    # create a cursor object
    cursor = cnx.cursor()
    # read the contents of the .sql file
    with open(filename, 'r') as file:
        sql_script = file.read()

        # extract the database's name from the script file
        start = sql_script.find("USE ") + 4
        if not start:
            start = sql_script.find("use ") + 4
        end_string = sql_script.find("a02;", start) + 3
        print(sql_script[start:end_string])
        database_name = sql_script[start:end_string]

    # execute the script using the cursor object
    for result in cursor.execute(sql_script, multi=True):
        pass
    # commit the changes to the database
    cnx.commit()

    # close the cursor and connection
    cursor.close()
    cnx.close()


def drop_table():
    """ Drop the student's database once the grading is done
    """
    # establish a connection to the database
    cnx = mysql.connector.connect(
        user='root', database=database_name, password='', host='localhost')
    # create a cursor object
    cursor = cnx.cursor()

    # execute the script using the cursor object
    cursor.execute("DROP DATABASE " + database_name)

    # close the cursor and connection
    cursor.close()
    cnx.close()
