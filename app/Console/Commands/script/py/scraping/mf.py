import os

from selenium.webdriver import Firefox, FirefoxOptions
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.ui import Select
from selenium.common.exceptions import TimeoutException

from dotenv import load_dotenv
import json

import pandas as pd


class mf:
    SECTION_INFO = {
        'cash': {
            'name_tr_id': 1,
            'value_tr_id': 2,
            'belongs_tr_id': 3,
            'table_css_selector': '#portfolio_det_depo > section > table'
        },
        'stock': {
            'name_tr_id': 2,
            'value_tr_id': 6,
            'belongs_tr_id': 10,
            'table_css_selector': '#portfolio_det_eq > table'
        },
        'trust': {
            'name_tr_id': 1,
            'value_tr_id': 5,
            'belongs_tr_id': 9,
            'table_css_selector': '#portfolio_det_mf > table'
        },
        'recievable': {
            'name_tr_id': 1,
            'value_tr_id': 2,
            'belongs_tr_id': 3,
            'table_css_selector': '#portfolio_det_bd > table'
        },
        'other': {
            'name_tr_id': 1,
            'value_tr_id': 3,
            'belongs_tr_id': 7,
            'table_css_selector': '#portfolio_det_oth > table'
        },
        'point': {
            'name_tr_id': 1,
            'value_tr_id': 5,
            'belongs_tr_id': 7,
            'table_css_selector': '#portfolio_det_po > table'
        },
        'fx': {
            'name_tr_id': 2,
            'value_tr_id': 3,
            'belongs_tr_id': 1,
            'table_css_selector': '#portfolio_det_fx > table.table.table-bordered.table-depo'
        },
    }

    def __init__(self):
        load_dotenv()

    def login(self):

        USER = os.environ["USER_NAME"]
        PASS = os.environ["PASSWORD"]

        # to get firefox's WebDriver
        options = FirefoxOptions()
        options.add_argument('-headless')
        browser = Firefox(options=options)

        # access to login page
        url_login = "https://moneyforward.com/users/sign_in"
        browser.get(url_login)
        print("success to access")

        # input textbox
        e = browser.find_element_by_css_selector("#sign_in_session_service_email")
        e.clear()
        e.send_keys(USER)
        e = browser.find_element_by_css_selector("#sign_in_session_service_password")
        e.clear()
        e.send_keys(PASS)
        print("fill login info")

        # send form
        frm = browser.find_element_by_css_selector("#login-btn-sumit")
        frm.submit()
        print("send login information")

        WebDriverWait(browser, 15).until(EC.presence_of_all_elements_located)


        try:
            WebDriverWait(browser, 10).until(
            EC.presence_of_element_located((By.CLASS_NAME, "keyword-authentication")))
            print("find keyword auth")
            self.__auth_keyword(browser)
        except TimeoutException as te:
            print("there is not any keyword auth")

        try:
            WebDriverWait(browser, 10).until(
            EC.presence_of_element_located((By.ID, "container_home_assets_balance")))
        except TimeoutException as te:
            print("fail to access mypage")

        return browser

    @staticmethod
    def __access_url(browser,url,check_selector_info):
        browser.get(url)
        try:
            WebDriverWait(browser, 10).until(
                EC.presence_of_element_located(
                    (check_selector_info["by"], check_selector_info["val"])))
            print("success to access " + url)
        except TimeoutException as te:
            print("Fail to access " + url + " or missing a keyword in the page.")

    def __auth_keyword(self,browser):
        url = "https://moneyforward.com/users/two_step_verifications/check_keyword"
        self.__access_url(browser, url, {"by":By.ID, "val":"keyword_secret_question_id"})

        QUESTION_VAL = os.environ["QUESTION_VAL"]
        ANSER = os.environ["QUESTION_ANSWER"]

        #質問選択
        select = Select(browser.find_element_by_id("keyword_secret_question_id"))
        select.select_by_value(QUESTION_VAL)

        #回答入力
        e = browser.find_element_by_id("keyword_answer")
        e.clear()
        e.send_keys(ANSER)

        # send form
        frm = browser.find_element_by_css_selector(".form-horizontal")
        frm.submit()
        print("send keyword")

        return

    def loadPortfolio(self, browser):
        # access to visitor log
        url_visitor_log = "https://moneyforward.com/bs/portfolio"
        browser.get(url_visitor_log)

        csv_array = []
        for section_name, section_info in self.SECTION_INFO.items():
            print("section:" + section_name)
            trs = browser.find_elements_by_css_selector(section_info['table_css_selector'] + " > tbody > tr")
            for tr in trs:
                row_array = {}
                td_list = tr.find_elements_by_css_selector("td")
                tr_id = 1
                for td in td_list:
                    if tr_id == self.SECTION_INFO[section_name]['name_tr_id']:
                        row_array['name'] = td.text
                    elif tr_id == self.SECTION_INFO[section_name]['value_tr_id']:
                        row_array['value'] = td.text.replace(',','');
                    elif tr_id == self.SECTION_INFO[section_name]['belongs_tr_id']:
                        row_array['account'] = td.text
                    # print(td.text)
                    tr_id += 1
                row_array['assign_rule'] = section_name
                print(row_array)
                csv_array.append(row_array)

        # 変換したいJSONファイルを読み込む
        df = pd.read_json(json.dumps(csv_array))

        # CSVに変換して任意のファイル名で保存
        df.to_csv("/tmp/asset.csv", index=False)
