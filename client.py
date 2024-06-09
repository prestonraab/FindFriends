import streamlit as st
from streamlit_geolocation import streamlit_geolocation
import js_eval

# Function to start watching the location
def start_watching_location():
    js_eval.start_watching_location().then(lambda _: st.session_state.update(watching=True))

# Function to retrieve the first location
def get_first_location():
    js_eval.get_first_location().then(lambda location: st.session_state.update(location=location))

# Function to update the location
def frequent_get_location():
    js_eval.get_latest_location().then(lambda location: st.session_state.update(location=location))

with st.form("my_form"):
   name = st.text_input('username')

   # Every form must have a submit button.
   submitted = st.form_submit_button("Submit")
   if submitted:
       st.write("name", name)

if 'watching' not in st.session_state:
    start_watching_location()

if not name:
  st.warning('Please input a name.')
  st.stop()

if 'location' not in st.session_state:
    get_first_location()
    st.warning('You have not given access to your location.')
    st.stop()

# Call frequent_get_location to update the location
frequent_get_location()
