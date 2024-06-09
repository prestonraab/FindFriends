import streamlit as st
from streamlit_geolocation import streamlit_geolocation
import streamlit_js_eval

with st.form("my_form"):
   name = st.text_input('username')

   # Every form must have a submit button.
   submitted = st.form_submit_button("Submit")
   if submitted:
       st.write("name", name)

if not name:
  st.warning('Please input a name.')
  st.stop()


if 'location' not in st.session_state:
    location = streamlit_js_eval.get_geolocation()
    if location:
        st.session_state['iter'] = 1
        st.session_state['location'] = location
        st.rerun()
    st.warning('You have not given access to your location.')
    st.stop()


def frequent_get_location():
    st.session_state['iter'] += 1
    iter = st.session_state['iter']
    streamlit_js_eval.get_geolocation(f"location{iter}")
    location = streamlit_js_eval.get_geolocation(f"location{iter - 1}")
    if location:
        st.session_state['location'] = location
    st.write(st.session_state['location'])

get_location = st.experimental_fragment(frequent_get_location, run_every=1)
get_location()
