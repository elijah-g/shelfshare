# Receives in book id.
# Returns a JSON list of matches in the following format.
# number: {
#             "BookID": 7,
#             "Distance": 20,
#             "SameSchool": false
#         }

#Import required modules
import mysql.connector
import sys
import requests
import json

#Connect to the database
mydb = mysql.connector.connect(
  host="localhost",
  user="happyluke",
  passwd="",
  database="SHELFSHARE"
)

mycursor = mydb.cursor()



 
#Get the id of the book which we're looking for a match for from the arguments
culprit_id = sys.argv[1]

#Get the isbn, status and ownerid of the book culprit.
mycursor.execute("SELECT ISBN, BookState, OwnerID FROM Books WHERE BookID = " + str(culprit_id))
culprit_result = mycursor.fetchone()

#assign the values for later use
culprit_isbn = culprit_result[0]
culprit_state = culprit_result[1]
culprit_owner_id = culprit_result[2]




#Get details of the culrpit owner
mycursor.execute("SELECT School, State, PCode FROM Accounts WHERE ID = " + str(culprit_owner_id))
c_owner_result = mycursor.fetchone()

#Assign the resulting values for later use
c_owner_school = c_owner_result[0]
c_owner_state = c_owner_result[1]
c_owner_pcode = c_owner_result[2]





#flip the state of the culprit to get the state of what we want to match
if culprit_state == 0:
    match_state = 1
elif culprit_state == 1:
    match_state = 0
else:
    print("ERROR: Book is already loaned.")
    exit()



#initialize list of matches
matches = []



#Find all matches of the same book.
mycursor.execute("SELECT BookID FROM Books WHERE BookState = " + str(match_state) + " AND ISBN = '" + str(culprit_isbn) + "'")


match_results = mycursor.fetchall()



#loop through results
for result in match_results:
    counter = 0
    results = {}
    result_id = result[0]
    
    
    
    #Get the details we need to know on the result
    mycursor.execute("SELECT Accounts.School, Accounts.State, Accounts.PCode \
    FROM Books INNER JOIN Accounts ON Books.OwnerID = Accounts.ID WHERE\
    Books.BookID = " + str(result_id))
    
    detail_result = mycursor.fetchone()
    
    
    #Assign Values for use
    result_school = detail_result[0]
    result_state = detail_result[1]
    result_pcode = detail_result[2]



    #Flag if they're at the same school
    school_flag = False
    if result_school == c_owner_school:
        school_flag = True


    
    
    #Get distance between culprit and match postcodes using the Mapquest distance api API
    #NEED TO ADD STATE FOR REQUEST TO WORK
    request = "http://www.mapquestapi.com/directions/v2/routematrix?key=ybDEiNsZwK4lS8tkg8estdqGEH0roboW&ambiguities=ignore&doReverseGeocode=false&routeType=fastest&unit=k&from="
    request = request + str(result_pcode)
    request = request + "%20" + str(result_state) + "%20Australia&to="
    request = request + str(c_owner_pcode)
    request = request + "%20" + str(c_owner_state) +"%20Australia"
    distance_request = requests.post(request)
    
    
    #Load the json we get back
    loaded_json = json.loads(distance_request.text)
    
    
    #Get the distance
    distance_between = loaded_json['distance'][1]
    
    
    #Load the results into a dictionary
    results["BookID"] = result[0]
    results["Distance"] = distance_between
    results["SameSchool"] = school_flag
    
    
    #Then add that dictionary to the matches list
    matches.append(results)


# Sort matches

sorted_matches = sorted(matches, key = lambda i: (-i['SameSchool'],i['Distance'])) 

sorted_json = json.dumps(sorted_matches)

print(sorted_json)