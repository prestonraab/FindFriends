import streamlit as st
from streamlit_geolocation import streamlit_geolocation
import js_eval

with st.form("my_form"):
   name = st.text_input('username')

   # Every form must have a submit button.
   submitted = st.form_submit_button("Submit")
   if submitted:
       st.write("name", name)

if not name:
  st.warning('Please input a name.')
  st.stop()

if 'watching' not in st.session_state:
    js_eval.start_watching_location()
    st.session_state['watching'] = True


if 'location' not in st.session_state:
    location = js_eval.get_latest_location()
    if location:
        st.session_state['iter'] = 0
        st.session_state['location'] = location
        st.rerun()
    st.warning('You have not given access to your location.')
    st.stop()


def frequent_get_location():
    st.session_state['iter'] += 1
    iter = st.session_state['iter']
    location = js_eval.get_latest_location()
    if location:
        st.write(st.session_state['location'])
        # st.session_state['location'] = location
    else:
        st.write(st.session_state['location'])


get_location = st.experimental_fragment(frequent_get_location, run_every=0.2)
get_location()
