import streamlit as st
from streamlit_geolocation import streamlit_geolocation

with st.form("my_form"):
   name = st.text_input('username')

   # Every form must have a submit button.
   submitted = st.form_submit_button("Submit")
   if submitted:
       st.write("name", name)

if not name:
  st.warning('Please input a name.')
  st.stop()

location = {}


@st.experimental_fragment(run_every=1)
def get_location():
    global location
    location = streamlit_geolocation()


if not location['latitude']:
  st.warning('You have not given access to your location.')
  st.stop()
else:
    st.write(location)


