import streamlit as st
from streamlit_geolocation import streamlit_geolocation
import js_eval


# Function to start watching the location
def start_watching_location():
    js_eval.start_watching_location()


# Function to retrieve the first location
def get_first_location():
    location = js_eval.get_first_location()
    st.session_state['location'] = location


# Function to update the location
def frequent_get_location():
    location = js_eval.get_latest_location()
    if location:
        st.session_state['location'] = location
    st.write(st.session_state['location'])


if 'watching' not in st.session_state:
    start_watching_location()
    st.session_state['watching'] = True
else:
    st.write("Watching")

if 'location' not in st.session_state:
    get_first_location()
    st.warning('You have not given access to your location.')
    st.stop()

# Call frequent_get_location to update the location
frequent_get_location()
