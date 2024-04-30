'''
Unit testing A02
- Install selenium (for unit testing webpages)
pip install selenium
pip3 install selenium


- Install mysql.connector (for unit testing mysql commands)
pip install mysql-connector-python
pip3 install mysql-connector-python

'''
from selenium import webdriver
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.common.by import By

from sql_import import submit_sql, drop_table
import time


# fill register form


def fill_field_register(field_details):
    """Fill the register.php page with values from field_details

    Args:
        field_details (list[str]): List of values to be filled in the register form.

    Returns:
        None
    """
    element = driver.find_element(By.TAG_NAME, "select")
    element.send_keys(field_details[4])
    element = driver.find_elements(By.TAG_NAME, "input")
    i = 0
    for e in element:
        if e.get_attribute("type") != "submit":
            e.clear()
            e.send_keys(field_details[i])
        elif e.get_attribute("type") == "submit":
            e.send_keys(Keys.RETURN)
            return
        i += 1

# fill profile form


def fill_field_profile(field_details):
    """Fill the profile.php page with values from field_details

    Args:
        field_details (list[str]): List of values to be filled in the profile form.

    Returns:
        None
    """
    elements = driver.find_elements(By.TAG_NAME, "input")
    i = 0
    element = driver.find_element(By.TAG_NAME, "select")
    element.send_keys(field_details[9])
    for e in elements:

        if e.get_attribute("type") != "submit" and e.get_attribute("type") != "radio" and e.get_attribute("type") != "hidden":
            e.clear()
            e.send_keys(field_details[i])
        elif e.get_attribute("type") == "radio" and i == 11:
            e.click()
        elif e.get_attribute("type") == "submit":
            driver.implicitly_wait(5)
            e.send_keys(Keys.RETURN)
            return
        i += 1

# fill index form


def fill_field_index(field_details):
    """Fill the index.php page with values from field_details

    Args:
        field_details (tuple[str, str]): Tuple containing the field name
        and value to be filled in the index form.

    Returns:
        None
    """
    element = driver.find_element("name", field_details[0])
    element.clear()
    element.send_keys(field_details[1])


def get_register_result(field_name: str) -> str:
    """Return the results retrieved from the field_name
    """
    element = driver.find_element("name", field_name)
    return element.get_attribute("value")


def get_profile_result(field_name: str) -> str:
    """Return the results retrieved from the field_name
    """
    if field_name == "avatar":
        elements = driver.find_elements("name", field_name)
        i = 0
        for e in elements:
            selected = e.is_selected()
            if selected and i == 2:
                return str(i)
            i += 1
    else:
        element = driver.find_element("name", field_name)
        return element.get_attribute("value")


def get_index_result():
    """Return the results retrieved from the posts
    """
    elements = driver.find_elements(By.TAG_NAME, "details")
    result = []
    for element in elements:
        result += [element.get_attribute("innerHTML")]
    return result


def unit_test(actual: str, expected: str) -> str:
    """Test if the actual value matches the expected value

    Args:
        actual (str): The actual value to be tested.
        expected (str): The expected value.

    Returns:
        str: "Pass" if the values match, "Fail" otherwise.
    """
    global num_pass, num_fail

    if actual == expected:
        num_pass += 1
        return "Pass"
    else:
        num_fail += 1
        return "Fail"


def unit_test_index(actual, expected: str) -> str:
    """Test if the actual value matches the expected value
    """
    global num_pass, num_fail

    if expected in actual:
        num_pass += 1
        return "Pass"
    else:
        num_fail += 1
        return "Fail"


def partial(lst, query: str):
    """Return the string that contains the partial string query

    Args:
        lst (list[str]): List of strings to search in.
        query (str): The partial string to search for.

    Returns:
        list[str]: List of strings containing the partial string.
    """
    return [s for s in lst if query in s]


# mian code
if __name__ == "__main__":

    # Files to be tested
    file_names = ["firstname_lastname_a02.sql", "register.php",
                  "profile.php", "index.php"]

    # Update according to where your website is located
    URL = "http://localhost/SYSC4504_w24/A02_Unit_Testing/"

    count = 0
    database_name = ""
    grade = 0
    for file in file_names:
        if ".sql" in file:
            # firstname_lastname_a02.sql
            try:
                print("\n************ Results for .sql file ************\n")

                # create the database and tables
                submit_sql(file)
                print("Database creation result: 2.5/2.5")
                grade += 2.5
            except:
                print("Failed importing database!")
                print("Database creation result: 0.0/2.5")
                grade += 0

            # Results for this test
            print(
                "\nSQL statements results: {0} / 100\n".format((grade/2.5) * 100))
        elif "register.php" in file:
            # Testing register.html
            num_pass = 0
            num_fail = 0
            try:
                # open page in a browser
                driver = webdriver.Firefox()  # change this to Chrome if you don't have Firefox

                driver.get(URL+file)
                driver.implicitly_wait(10)
                register_fields = {"first_name": "John", "last_name": "Snow", "DOB": "001010-10-10",
                                   "student_email": "johnsnow@mail.com", "program": "Computer Systems Engineering"}

                # Fill the form
                fill_field_register(list(register_fields.values()))

                # Test if the filled form worked
                print("Score for {0}: ".format(file))

                time.sleep(5)
                for item in register_fields.items():
                    if item[0] == "DOB":
                        print("testing field {0}: {1}".format(
                            item[0], unit_test(get_register_result(item[0]), item[1][2::])))
                    else:
                        print("testing field {0}: {1}".format(
                            item[0], unit_test(get_register_result(item[0]), item[1])))
                print("In register.php:\n\t Number of tests {0}: {1} passed and {2} failed.".format(
                    num_pass+num_fail, num_pass, num_fail))
            except:
                print("Fail: ", file)

            # Results this test
            print("Register results: {0}/1.5".format((num_pass/5)*1.5))
            grade += (num_pass/5) * 1.5

        elif "profile.php" in file:
            # Testing profile.php
            num_pass = 0
            num_fail = 0
            try:
                # open page in a browser
                driver.get(URL+file)
                driver.implicitly_wait(5)
                driver.refresh()
                profile_fields = {"first_name": "john", "last_name": "snow", "DOB": "001011-10-10", "street_number": "1234", "street_name": "colonel", "city": "The North",
                                  "province": "ON", "postal_code": "k1s5b6", "student_email": "john.snow@mail.com", "program": "Electrical Engineering", "avatar": "2"}

                # Fill the form
                fill_field_profile(list(profile_fields.values()))

                # Test if the filled form worked
                print("Score for {0}: ".format(driver.current_url))

                time.sleep(5)
                for item in profile_fields.items():
                    if item[0] == "DOB":
                        print("testing field {0}: {1}".format(
                            item[0], unit_test(get_register_result(item[0]), item[1][2::])))
                    else:
                        print("testing field {0}: {1}".format(
                            item[0], unit_test(get_profile_result(item[0]), item[1])))
                print("In profile.php:\n\t Number of tests {0}: {1} passed and {2} failed.".format(
                    num_pass+num_fail, num_pass, num_fail))
            except:
                print("Fail: ", file)

            # Results this test
            print("Profile results: {0}/2".format((num_pass/11)*2))
            grade += (num_pass/11) * 2
        elif "index.php" in file:
            # Testing index.php
            num_pass = 0
            num_fail = 0
            try:
                driver.get(URL + file)
                driver.implicitly_wait(5)
                index_fields = {"new_post1": "This is post 1", "new_post2": "This is post 2", "new_post3": "This is post 3",
                                "new_post4": "This is post 4", "new_post5": "This is post 5", "new_post6": "This is post 6"}

                # Fill the form
                for item in index_fields.items():
                    post = (item[0][:8], item[1])
                    fill_field_index(post)
                    submit = driver.find_element(By.TAG_NAME, "input")
                    submit.click()

                # Test if the filled form worked
                print("Score for {0}: ".format(driver.current_url))

                time.sleep(5)
                results = get_index_result()
                for item in index_fields.items():
                    test = partial(results, item[1])
                    if test:
                        print("testing field {0}: {1}".format(
                            item[0], unit_test_index(partial(results, item[1])[0], item[1])))
                print("In index.php:\n\t Number of tests {0}: {1} passed and {2} failed.".format(
                    num_pass+num_fail, num_pass, num_fail))
            except:
                print("Fail: ", file)

            # Results this test
            print("Index results: {0}/1.5".format((num_pass/5)*1.5))
            grade += num_pass / 5 * 1.5
            driver.close()

            # Final Assignment Results
    print("Final A02 score is: {0}/7.5". format(grade))
